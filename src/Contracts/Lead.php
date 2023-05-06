<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Lead
{
    /**
     * Get the lead's unique identifier.
     */
    public function getIdentifier(): string|int;
}
