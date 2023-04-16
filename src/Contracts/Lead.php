<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Lead
{
    /**
     * Get the lead's unique identifier.
     *
     * @return string|int
     */
    public function getIdentifier(): string|int;

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
     * Whether the lead has an attribute.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool;

    /**
     * Get the lead's attributes.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array;
}
