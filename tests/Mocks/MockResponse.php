<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use GrayMatterLabs\PingTree\Contracts\Response as ResponseContract;

class MockResponse implements ResponseContract
{
    public function __construct(private bool $success, private bool $accepted)
    {
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function accepted(): bool
    {
        return $this->accepted;
    }
}
