<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;

final class Shuffled implements Strategy
{
    public function __construct(private Strategy $strategy)
    {
    }

    /**
     * @param array<Offer> $offers
     */
    public function execute(Lead $lead, array $offers): Offer
    {
        shuffle($offers);

        return $this->strategy->execute($lead, $offers);
    }
}
