<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;

class MockStrategy implements Strategy
{
    public function execute(Lead $lead, array $offers): Offer
    {
        return array_values($offers)[0];
    }
}
