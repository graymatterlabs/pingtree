<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

use Closure;

trait HasEvents
{
    protected array $listeners = [];

    public function listen(string $event, Closure $closure): void
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
