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

    /**
     * Get an attribute from the lead.
     *
     * @param string $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, mixed $default = null): mixed;

    /**
     * Determine whether the lead has an attribute.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool;

    /**
     * Get the leads attributes.
     *
     * @return array
     */
    public function getAttributes(): array;
}
