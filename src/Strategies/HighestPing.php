<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;

class HighestPing implements Strategy
{
    protected array $pings = [];

    public function execute(Lead $lead, array $offers): Offer
    {
        return array_reduce($offers, function (?Offer $previous, Offer $offer) use ($lead) {
            // prevents pinging in the case that there is only one offer
            if (is_null($previous)) {
                return $offer;
            }

            return $this->ping($lead, $previous) > $this->ping($lead, $offer)
                ? $previous
                : $offer;
        });
    }

    protected function ping(Lead $lead, Offer $offer): int
    {
        // prevents pinging multiple times for the same lead, during the same execution
        if (! isset($this->pings[$lead->getIdentifier()][$offer->getIdentifier()])) {
            $this->pings[$lead->getIdentifier()][$offer->getIdentifier()] = $offer->ping($lead);
        }

        return $this->pings[$lead->getIdentifier()][$offer->getIdentifier()];
    }
}
