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
        protected string $name,
        protected Response $response,
        protected int $score = 1,
        protected array $ineligible = [],
        protected bool $healthy = true,
        protected ?Closure $sendCallback = null,
        protected ?Closure $notifyCallback = null
    ) {
    }

    public function setResponse(Response $response): static
    {
        $this->response = $response;

        return $this;
    }

    public function setIneligible(Lead $lead): static
    {
        $this->ineligible[] = $lead->getHash();

        return $this;
    }

    public function setHealthy(bool $healthy): static
    {
        $this->healthy = $healthy;

        return $this;
    }

    public function getIdentifier(): string|int
    {
        return $this->name;
    }

    public function ping(Lead $lead): int
    {
        return $this->score;
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
        return ! in_array($lead->getHash(), $this->ineligible);
    }

    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    public function notifyOfFailure(Lead $lead, Response $response): void
    {
        $callback = $this->notifyCallback;

        if ($callback) {
            $callback($lead, $response);
            $this->healthy = false;

            return;
        }

        $response->wasRejected()
            ? $this->ineligible[] = $lead->getHash()
            : $this->healthy = false;
    }
}
