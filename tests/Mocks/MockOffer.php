<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use Closure;
use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Response;

class MockOffer implements Offer
{
    public function __construct(
        private string   $name,
        private Response $response,
        private int      $ping = 1,
        private array    $ineligible = [],
        private bool     $healthy = true,
        private ?Closure $sendCallback = null,
    ) {
    }

    public function getIdentifier(): string|int
    {
        return $this->name;
    }

    public function ping(Lead $lead): int
    {
        return $this->ping;
    }

    public function send(Lead $lead): Response
    {
        $callback = $this->sendCallback;

        if ($callback) {
            $callback($lead);
        }

        return $this->response;
    }

    public function isEligible(Lead $lead): bool
    {
        return ! in_array($lead->getIdentifier(), $this->ineligible);
    }

    public function isHealthy(): bool
    {
        return $this->healthy;
    }
}
