<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests;

use GrayMatterLabs\PingTree\Contracts\Response;
use GrayMatterLabs\PingTree\Exceptions\NoOffers;
use GrayMatterLabs\PingTree\Strategies\Ordered;
use GrayMatterLabs\PingTree\Tests\Mocks\MockLead;
use GrayMatterLabs\PingTree\Tests\Mocks\MockOffer;
use GrayMatterLabs\PingTree\Tests\Mocks\MockResponse;
use GrayMatterLabs\PingTree\Tree;
use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{
    public function test_it_sends_a_lead_to_an_offer(): void
    {
        $lead = new MockLead('id');

        $tree = $this->getTree([
            new MockOffer(
                'offer-name',
                new MockResponse(true, true),
                sendCallback: fn ($sending) => $this->assertSame($lead, $sending)
            ),
        ]);

        $tree->ping($lead);
    }

    public function test_it_throws_an_exception_if_there_are_no_offers(): void
    {
        $lead = new MockLead('id');

        $tree = $this->getTree();

        $this->expectException(NoOffers::class);

        $tree->ping($lead);
    }

    public function test_it_throws_an_exception_if_there_are_no_healthy_offers(): void
    {
        $lead = new MockLead('id');

        $tree = $this->getTree([
            new MockOffer('offer-name', new MockResponse(true, true), healthy: false),
        ]);

        $this->expectException(NoOffers::class);

        $tree->ping($lead);
    }

    public function test_it_throws_an_exception_if_there_are_no_eligible_offers(): void
    {
        $lead = new MockLead('id');

        $tree = $this->getTree([
            new MockOffer('offer-name', new MockResponse(true, true), ineligible: [$lead->getIdentifier()]),
        ]);

        $this->expectException(NoOffers::class);

        $tree->ping($lead);
    }

    public function test_if_an_offer_becomes_unhealthy_it_gets_another(): void
    {
        $lead = new MockLead('id');

        $offers = [
                new MockOffer('offer-name', new MockResponse(true, true), healthy: false),
                new MockOffer('offer-name2', new MockResponse(true, true)),
        ];

        $tree = $this->getTree($offers);

        $events = [];
        $selected = null;

        $tree->listen('sending', function ($lead, $offer) use (&$events) {
            $events[] = $offer;
        });

        $tree->listen('sent', function ($lead, $offer, $response) use (&$selected) {
            if ($response->accepted()) {
                $selected = $offer;
            }
        });

        $tree->ping($lead);

        $this->assertCount(1, $events);
        $this->assertSame($offers[1], $selected);
    }

    public function test_if_a_lead_is_ineligible_for_an_offer_it_gets_another(): void
    {
        $lead = new MockLead('id');

        $offers = [
            new MockOffer('offer-name', new MockResponse(true, true), ineligible: [$lead->getIdentifier()]),
            new MockOffer('offer-name2', new MockResponse(true, true)),
        ];

        $tree = $this->getTree($offers);

        $events = [];
        $selected = null;

        $tree->listen('sending', function ($lead, $offer) use (&$events) {
            $events[] = $offer;
        });

        $tree->listen('sent', function ($lead, $offer, $response) use (&$selected) {
            if ($response->accepted()) {
                $selected = $offer;
            }
        });

        $tree->ping($lead);

        $this->assertCount(1, $events);
        $this->assertSame($offers[1], $selected);
    }

    public function test_if_an_offer_fails_it_tries_another(): void
    {
        $lead = new MockLead('id');

        $offers = [
            new MockOffer('offer-name', new MockResponse(false, false)),
            new MockOffer('offer-name2', new MockResponse(true, true)),
        ];

        $tree = $this->getTree($offers);

        $events = [];
        $selected = null;

        $tree->listen('sending', function ($lead, $offer) use (&$events) {
            $events[] = $offer;
        });

        $tree->listen('sent', function ($lead, $offer, $response) use (&$selected) {
            if ($response->accepted()) {
                $selected = $offer;
            }
        });

        $tree->ping($lead);

        $this->assertCount(2, $events);
        $this->assertSame($offers[1], $selected);
    }

    /** @dataProvider providesResponsesAndEvents */
    public function test_it_fires_events_in_expected_order(Response $response, array $events): void
    {
        $lead = new MockLead('id');

        $tree = $this->getTree([
            new MockOffer('offer-name', $response),
        ]);

        $emitted = [];

        foreach ($events as $event) {
            $tree->listen($event, function () use (&$emitted, $event) {
                $emitted[] = $event;
            });
        }

        if (! $response->accepted()) {
            $this->expectException(NoOffers::class);
        }

        $tree->ping($lead);

        foreach ($events as $order => $event) {
            $this->assertEquals($event, $emitted[$order]);
        }
    }

    public function providesResponsesAndEvents(): array
    {
        return [
            'Accepted lead' => [new MockResponse(true, true), [
                'selecting',
                'selected',
                'sending',
                'sent',
            ]],

            'Failed offer' => [new MockResponse(false, false), [
                'selecting',
                'selected',
                'sending',
                'sent',
            ]],

            'Rejected lead' => [new MockResponse(true, false), [
                'selecting',
                'selected',
                'sending',
                'sent',
            ]],
        ];
    }

    private function getTree(array $offers = []): Tree
    {
        return new Tree(new Ordered(), $offers, 1);
    }
}
