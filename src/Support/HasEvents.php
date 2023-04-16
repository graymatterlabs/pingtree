<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

trait HasEvents
{
    protected array $listeners = [];

    public function listen(string $event, callable $callable): void
    {
        $this->listeners[$event][] = $callable;
    }

    protected function event(string $event, ...$arguments): void
    {
        foreach ($this->listeners($event) as $listener) {
            $listener(...$arguments);
        }
    }

    protected function listeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}
