<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;

final class Ordered implements Strategy
{
    /**
     * @param array<Offer> $offers
     */
    public function execute(Lead $lead, array $offers): Offer
    {
        return array_values($offers)[0];
    }
}
