<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests;

use GrayMatterLabs\PingTree\Exceptions\NoOffersException;
use GrayMatterLabs\PingTree\Tests\Mocks\MockLead;
use GrayMatterLabs\PingTree\Tests\Mocks\MockOffer;
use GrayMatterLabs\PingTree\Tests\Mocks\MockResponse;
use GrayMatterLabs\PingTree\Tests\Mocks\MockStrategy;
use GrayMatterLabs\PingTree\Tests\Mocks\MockTree;
use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{
    public function test_it_sends_a_lead_to_an_offer(): void
    {
        $lead = new MockLead('hash');

        $tree = $this->getTree([
            new MockOffer(
                'offer-name',
                new MockResponse(),
                sendCallback: fn ($sending) => $this->assertSame($lead, $sending)
            ),
        ]);

        $tree->ping($lead);
    }

    public function test_it_throws_an_exception_if_there_are_no_offers(): void
    {
        $lead = new MockLead('hash');

        $tree = $this->getTree([]);

        $this->expectException(NoOffersException::class);

        $tree->ping($lead);
    }

    public function test_it_throws_an_exception_if_there_are_no_healthy_offers(): void
    {
        $lead = new MockLead('hash');

        $tree = $this->getTree([
            new MockOffer('offer-name', new MockResponse(), healthy: false),
        ]);

        $this->expectException(NoOffersException::class);

        $tree->ping($lead);
    }

    public function test_it_throws_an_exception_if_there_are_no_eligible_offers(): void
    {
        $lead = new MockLead('hash');

        $tree = $this->getTree([
            new MockOffer('offer-name', new MockResponse(), ineligible: [$lead->getIdentifier()]),
        ]);

        $this->expectException(NoOffersException::class);

        $tree->ping($lead);
    }

    public function test_if_an_offer_becomes_unhealthy_it_gets_another(): void
    {
        $lead = new MockLead('hash');

        $offers = [
                new MockOffer('offer-name', new MockResponse(), healthy: false),
                new MockOffer('offer-name', new MockResponse()),
        ];

        $tree = $this->getTree($offers);

        $tree->ping($lead);

        $this->assertSame($offers[1], $tree->getSelectedOffer());
    }

    public function test_if_a_lead_is_ineligible_for_an_offer_it_gets_another(): void
    {
        $lead = new MockLead('hash');

        $offers = [
            new MockOffer('offer-name', new MockResponse(), ineligible: [$lead->getIdentifier()]),
            new MockOffer('offer-name', new MockResponse()),
        ];

        $tree = $this->getTree($offers);

        $tree->ping($lead);

        $this->assertSame($offers[1], $tree->getSelectedOffer());
    }

    public function test_if_an_offer_fails_it_gets_another(): void
    {
        $lead = new MockLead('hash');

        $offers = [
            new MockOffer('offer-name', new MockResponse(true, false, true)),
            new MockOffer('offer-name', new MockResponse()),
        ];

        $tree = $this->getTree($offers);

        $tree->ping($lead);

        $this->assertSame($offers[1], $tree->getSelectedOffer());
    }

    public function test_it_notifies_an_offer_of_a_failure(): void
    {
        $lead = new MockLead('hash');

        $offers = [
            new MockOffer(
                'offer-name',
                new MockResponse(true, false, true),
                notifyCallback: fn () => $this->addToAssertionCount(1)
            ),

            new MockOffer('offer-name', new MockResponse()),
        ];

        $tree = $this->getTree($offers);

        $tree->ping($lead);
    }

    protected function getTree(array $offers = []): MockTree
    {
        return new MockTree(new MockStrategy(), $offers);
    }
}
