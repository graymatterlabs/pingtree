<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use GrayMatterLabs\PingTree\Contracts\Response;

class MockResponse implements Response
{
    public function __construct(
        protected bool $failed = false,
        protected bool $wasRejected = false,
        protected bool $shouldRetry = false
    ) {
    }

    public function failed(): bool
    {
        return $this->failed;
    }

    public function shouldRetry(): bool
    {
        return $this->shouldRetry;
    }

    public function wasRejected(): bool
    {
        return $this->wasRejected;
    }
}
