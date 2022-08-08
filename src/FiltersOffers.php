<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;

trait FiltersOffers
{
    protected function filterUnhealthyOffers(array $offers): array
    {
        return array_filter($offers, static fn (Offer $offer) => $offer->isHealthy());
    }

    protected function filterIneligibleOffers(Lead $lead, array $offers): array
    {
        return array_filter($offers, static fn (Offer $offer) => $offer->isEligible($lead));
    }
}
