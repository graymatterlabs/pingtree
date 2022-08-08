<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Lead
{
    /**
     * Get the hash representing the lead.
     *
     * @return string
     */
    public function getHash(): string;
}
