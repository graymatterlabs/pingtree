<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Strategy
{
    /**
     * Get the offer chosen by the strategy.
     *
     * @param \GrayMatterLabs\PingTree\Contracts\Lead $lead
     * @param \GrayMatterLabs\PingTree\Contracts\Offer[] $offers
     *
     * @return \GrayMatterLabs\PingTree\Contracts\Offer
     */
    public function execute(Lead $lead, array $offers): Offer;
}
