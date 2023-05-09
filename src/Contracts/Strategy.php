<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Strategy
{
    /**
     * Get the offer chosen by the strategy.
     *
     * @param array<Offer> $offers
     */
    public function execute(Lead $lead, array $offers): Offer;
}
