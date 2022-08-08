<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Response;
use GrayMatterLabs\PingTree\Tree;

class MockTree extends Tree
{
    protected int $maxTries = 1;

    protected int $backoff = 0;

    protected Offer $selectedOffer;

    public function handleAfterSending(Lead $lead, Offer $offer, int $attempts, Response $response): void
    {
        $this->selectedOffer = $offer;
    }

    public function getSelectedOffer(): Offer
    {
        return $this->selectedOffer;
    }
}
