<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;

class Shuffled implements Strategy
{
    public function __construct(protected Strategy $strategy)
    {
    }

    public function execute(Lead $lead, array $offers): Offer
    {
        shuffle($offers);

        return $this->strategy->execute($lead, $offers);
    }
}
