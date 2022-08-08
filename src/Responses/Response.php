<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Responses;

use GrayMatterLabs\PingTree\Contracts\Response as ResponseContract;

class Response implements ResponseContract
{
    public function __construct(protected bool $failed, protected bool $wasRejected)
    {
    }

    public function failed(): bool
    {
        return $this->failed;
    }

    public function wasRejected(): bool
    {
        return $this->wasRejected;
    }

    public function shouldRetry(): bool
    {
        return $this->failed && ! $this->wasRejected;
    }
}
