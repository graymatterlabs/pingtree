<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Response
{
    /**
     * Whether the request was successful.
     */
    public function success(): bool;

    /**
     * Whether the lead was accepted.
     */
    public function accepted(): bool;
}
