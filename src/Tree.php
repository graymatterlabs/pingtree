<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree;

use GrayMatterLabs\PingTree\Contracts\Backoff;
use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Response;
use GrayMatterLabs\PingTree\Contracts\Strategy;
use GrayMatterLabs\PingTree\Exceptions\NoOffersException;
use GrayMatterLabs\PingTree\Support\HasEvents;
use GrayMatterLabs\PingTree\Support\LinearBackoff;
use GrayMatterLabs\PingTree\Support\Offers;

class Tree
{
    use HasEvents;

    /**
     * The number of times to attempt sending a lead to an offer.
     *
     * @var int
     */
    public int $maxTries = 3;

    /**
     * The offers in the ping tree.
     *
     * @var Offers
     */
    protected Offers $offers;

    /**
     * The strategy used to backoff between attempts to send to an offer.
     *
     * @var Backoff
     */
    protected Backoff $backoff;

    public function __construct(protected Strategy $strategy, array $offers, Backoff $backoff = null)
    {
        $this->offers = Offers::wrap($offers);
        $this->backoff = $backoff ?? new LinearBackoff();
    }

    public function ping(Lead $lead, array $except = []): Response
    {
        $offers = $this->offers
            ->except($except)
            ->healthy()
            ->eligible($lead)
            ->unique();

        if ($offers->isEmpty()) {
            throw new NoOffersException($lead);
        }

        $this->event('pinging', $this->strategy, $lead, $offers->toArray());

        $offer = $this->strategy->execute($lead, $offers->toArray());

        $response = $this->send($lead, $offer);

        if (! $response->accepted()) {
            return $this->ping($lead, [...$except, $offer]);
        }

        return $response;
    }

    protected function send(Lead $lead, Offer $offer): Response
    {
        $attempt = 1;

        $this->event('sending', $lead, $offer);

        do {
            $this->event('attempting', $lead, $offer, $attempt);

            $response = $offer->send($lead);

            $this->handleResponse($lead, $offer, $response, $attempt);
        } while (! $response->success() && $this->backoffAndRetry($attempt++));

        return $response;
    }

    protected function handleResponse(Lead $lead, Offer $offer, Response $response, int $attempt): void
    {
        switch (true) {
            case ! $response->success():
                $this->event('failed', $lead, $offer, $response, $attempt);

                break;
            case ! $response->accepted():
                $this->event('rejected', $lead, $offer, $response, $attempt);

                break;
            default:
                $this->event('accepted', $lead, $offer, $response, $attempt);

                break;
        }
    }

    protected function backoffAndRetry(int $attempt): bool
    {
        if ($attempt >= $this->maxTries) {
            return false;
        }

        sleep($this->backoff->getWaitInSeconds($attempt));

        return true;
    }
}
