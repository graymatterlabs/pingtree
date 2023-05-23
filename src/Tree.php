<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Response;
use GrayMatterLabs\PingTree\Contracts\Strategy;
use GrayMatterLabs\PingTree\Exceptions\NoOffers;
use GrayMatterLabs\PingTree\Support\BackoffAndRetry;
use GrayMatterLabs\PingTree\Support\HasEvents;
use GrayMatterLabs\PingTree\Support\Offers;

final class Tree
{
    use HasEvents;

    /**
     * @param array<Offer> $offers
     */
    public function __construct(private Strategy $strategy, private array $offers)
    {
    }

    /**
     * Send the lead through the ping tree.
     *
     * @param array<Offer> $except
     *
     * @throws NoOffers
     */
    public function ping(Lead $lead, array $except = []): Response
    {
        $offers = Offers::wrap($this->offers)
            ->except($except)
            ->healthy()
            ->eligible($lead)
            ->unique();

        if ($offers->isEmpty()) {
            throw new NoOffers($lead);
        }

        $this->dispatch('selecting', $this->strategy, $lead, $offers->toArray());

        $offer = $this->strategy->execute($lead, $offers->toArray());

        $this->dispatch('selected', $lead, $offers->toArray(), $offer);

        $response = $this->send($lead, $offer);

        if (! $response->accepted()) {
            return $this->ping($lead, [...$except, $offer]);
        }

        return $response;
    }

    /**
     * Attempt to send the lead to the offer.
     */
    private function send(Lead $lead, Offer $offer): Response
    {
        $attempts = 0;

        $this->dispatch('sending', $lead, $offer);

        do {
            $response = $offer->send($lead);
            $attempts++;
        } while (! $response->success() && (new BackoffAndRetry())->handle($attempts));

        $this->dispatch('sent', $lead, $offer, $response, $attempts);

        return $response;
    }
}
