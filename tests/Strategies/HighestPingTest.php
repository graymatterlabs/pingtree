<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Strategies;

use GrayMatterLabs\PingTree\Strategies\HighestPing;
use GrayMatterLabs\PingTree\Tests\Mocks\MockLead;
use GrayMatterLabs\PingTree\Tests\Mocks\MockOffer;
use GrayMatterLabs\PingTree\Tests\Mocks\MockResponse;
use PHPUnit\Framework\TestCase;

class HighestPingTest extends TestCase
{
    public function test_it_gets_the_offer_with_highest_ping(): void
    {
        $lead = new MockLead('hash');

        $offers = [
            new MockOffer('offer-name', new MockResponse(true, true), 1),
            new MockOffer('offer-name2', new MockResponse(true, true), 2),
            new MockOffer('offer-name3', new MockResponse(true, true), 0),
        ];

        $offer = (new HighestPing())->execute($lead, $offers);

        $this->assertEquals('offer-name2', $offer->getIdentifier());
    }
}
