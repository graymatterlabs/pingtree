<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Response
{
    /**
     * Whether the request to the offer was successful.
     */
    public function success(): bool;

    /**
     * Whether the lead was accepted by the offer.
     */
    public function accepted(): bool;
}
