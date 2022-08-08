<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Responses;

class RedirectResponse extends Response
{
    public function __construct(protected bool $failed, protected bool $wasRejected, protected ?string $redirect = null)
    {
    }

    public function getRedirect(): ?string
    {
        return $this->redirect;
    }
}
