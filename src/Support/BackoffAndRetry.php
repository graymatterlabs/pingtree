<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

final class BackoffAndRetry
{
    private static int $maxAttempts = 3;

    /**
     * Whether to retry sending the lead to the offer and optionally sleep if so.
     */
    public function handle(int $attempt): bool
    {
        if ($attempt >= self::$maxAttempts) {
            return false;
        }

        sleep($attempt);

        return true;
    }

    public static function setMaxAttempts(int $attempts): void
    {
        self::$maxAttempts = $attempts;
    }
}
