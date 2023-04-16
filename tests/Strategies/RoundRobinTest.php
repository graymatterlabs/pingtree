<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Strategies;

use GrayMatterLabs\PingTree\Strategies\HighestPing;
use GrayMatterLabs\PingTree\Strategies\RoundRobin;
use GrayMatterLabs\PingTree\Tests\Mocks\MockLead;
use GrayMatterLabs\PingTree\Tests\Mocks\MockOffer;
use GrayMatterLabs\PingTree\Tests\Mocks\MockResponse;
use GrayMatterLabs\SimpleCache\ArrayCache;
use PHPUnit\Framework\TestCase;

class RoundRobinTest extends TestCase
{
    public function test_it_rotates_through_offers_using_the_given_strategy(): void
    {
        $lead = new MockLead('hash');

        $offers = [
            new MockOffer('offer-name', new MockResponse(true, true), 1),
            new MockOffer('offer-name2', new MockResponse(true, true), 2),
            new MockOffer('offer-name3', new MockResponse(true, true), 0),
        ];

        $strategy = new RoundRobin(new ArrayCache(), new HighestPing());

        $selected = [];

        for ($i = 0; $i < count($offers); $i++) {
            $selected[] = $strategy->execute($lead, $offers)->getIdentifier();
        }

        $this->assertEquals('offer-name2', $selected[0]);
        $this->assertEquals('offer-name', $selected[1]);
        $this->assertEquals('offer-name3', $selected[2]);
    }

    public function test_it_resets_once_running_out_of_offers(): void
    {
        $lead = new MockLead('hash');

        $offers = [
            new MockOffer('offer-name', new MockResponse(true, true)),
        ];

        $strategy = new RoundRobin(new ArrayCache(), new HighestPing());

        $selected = [];

        for ($i = 0; $i < 2; $i++) {
            $selected[] = $strategy->execute($lead, $offers)->getIdentifier();
        }

        $this->assertCount(2, $selected);
    }
}
