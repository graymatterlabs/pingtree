<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

trait HasEvents
{
    protected array $listeners = [];

    public function listen(string $event, callable $closure): void
    {
        $this->listeners[$event][] = $closure;
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
