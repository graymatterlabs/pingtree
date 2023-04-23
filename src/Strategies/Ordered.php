<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;

class Ordered implements Strategy
{
    public function execute(Lead $lead, array $offers): Offer
    {
        return array_values($offers)[0];
    }
}
