<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Response
{
    /**
     * Whether the request was successful.
     *
     * @return bool
     */
    public function success(): bool;

    /**
     * Whether the lead was accepted.
     *
     * @return bool
     */
    public function accepted(): bool;
}
