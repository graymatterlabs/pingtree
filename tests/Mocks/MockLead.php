<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use GrayMatterLabs\PingTree\Contracts\Lead;

class MockLead implements Lead
{
    public function __construct(protected string $identifier)
    {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $default;
    }

    public function hasAttribute(string $name): bool
    {
        return false;
    }

    public function getAttributes(): array
    {
        return [];
    }
}
