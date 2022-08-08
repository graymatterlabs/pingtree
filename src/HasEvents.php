<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree;

use Closure;

trait HasEvents
{
    protected function withEvents(string $action, Closure $closure, ...$arguments): mixed
    {
        $this->event('before.'.$action, ...$arguments);

        $response = $closure(...$arguments);

        $this->event('after.'.$action, ...[...$arguments, $response]);

        return $response;
    }

    protected function event(string $event, ...$arguments): void
    {
        $method = $this->getMethodForEvent($event);

        if (method_exists($this, $method)) {
            $this->$method(...$arguments);
        }
    }

    public function getMethodForEvent(string $event): string
    {
        return 'handle' . implode('', array_map(fn ($value) => ucfirst($value), explode('.', $event)));
    }
}
