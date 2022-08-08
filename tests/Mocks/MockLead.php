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
}
