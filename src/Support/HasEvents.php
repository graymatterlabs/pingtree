<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

trait HasEvents
{
    /** @var array<string, array<callable>> */
    protected array $listeners = [];

    /**
     * Register a listener for the event.
     */
    public function listen(string $event, callable $callable): void
    {
        $this->listeners[$event][] = $callable;
    }

    /**
     * Dispatch the event with the arguments provided.
     */
    protected function event(string $event, ...$arguments): void
    {
        foreach ($this->listeners($event) as $listener) {
            $listener(...$arguments);
        }
    }

    /**
     * Get the listeners for the event.
     *
     * @return array<callable>
     */
    protected function listeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}
