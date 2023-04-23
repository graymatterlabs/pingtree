<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Backoff
{
    public function getWaitInSeconds(int $attempt): int;
}
