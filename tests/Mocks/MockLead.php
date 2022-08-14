<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use GrayMatterLabs\PingTree\Contracts\Lead;

class MockLead implements Lead
{
    public function __construct(protected string $hash)
    {
    }

    public function getHash(): string
    {
        return $this->hash;
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
