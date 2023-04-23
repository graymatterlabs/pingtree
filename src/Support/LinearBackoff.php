<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

use GrayMatterLabs\PingTree\Contracts\Backoff;

final class LinearBackoff implements Backoff
{
    /**
     * @param int $base The number of seconds to multiply by when linearly backing off after a failed send.
     */
    public function __construct(protected int $base = 1)
    {
    }

    public function getWaitInSeconds(int $attempt): int
    {
        return $attempt * $this->base;
    }
}
