<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Response
{
    /**
     * Whether sending the lead failed.
     *
     * @return bool
     */
    public function failed(): bool;

    /**
     * Whether the lead was rejected.
     *
     * @return bool
     */
    public function wasRejected(): bool;

    /**
     * Whether sending the lead should be retried.
     *
     * @return bool
     */
    public function shouldRetry(): bool;
}
