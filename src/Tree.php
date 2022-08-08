<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Response;
use GrayMatterLabs\PingTree\Contracts\Strategy;
use GrayMatterLabs\PingTree\Exceptions\NoOffersException;

class Tree
{
    use HasEvents;
    use FiltersOffers;

    /**
     * The number of times to attempt sending a lead to an offer.
     *
     * @var int
     */
    protected int $maxTries = 3;

    /**
     * The number of seconds to multiply by when exponentially backing off after a failed send.
     *
     * @var int
     */
    protected int $backoff = 1;

    public function __construct(protected Strategy $strategy, protected array $offers)
    {
    }

    public function ping(Lead $lead): Response
    {
        $offers = $this->filterIneligibleOffers(
            $lead,
            $this->filterUnhealthyOffers($this->offers)
        );

        if (empty($offers)) {
            throw new NoOffersException($lead);
        }

        /** @var \GrayMatterLabs\PingTree\Contracts\Offer $offer */
        $offer = $this->withEvents('pinging', function (Strategy $strategy, Lead $lead, array $offers) {
            return $strategy->execute($lead, $offers);
        }, $this->strategy, $lead, $offers);

        $response = $this->send($lead, $offer);

        if ($response->failed()) {
            return $this->ping($lead);
        }

        return $response;
    }

    protected function send(Lead $lead, Offer $offer): Response
    {
        $tries = 1;

        do {
            /** @var \GrayMatterLabs\PingTree\Contracts\Response $response */
            $response = $this->withEvents('sending', function (Lead $lead, Offer $offer, int $attempt) {
                $this->backoff($attempt);

                return $offer->send($lead);
            }, $lead, $offer, $tries);
        } while ($response->shouldRetry() && $tries++ < $this->maxTries);

        if ($response->failed()) {
            $offer->notifyOfFailure($lead, $response);
            $this->event('offer.failed', $lead, $offer, $response);
        }

        return $response;
    }

    protected function backoff(int $attempt): void
    {
        sleep(--$attempt * $this->backoff);
    }
}
