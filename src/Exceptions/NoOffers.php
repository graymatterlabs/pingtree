<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Exceptions;

use Exception;
use GrayMatterLabs\PingTree\Contracts\Lead;

final class NoOffers extends Exception
{
    public function __construct(Lead $lead)
    {
        parent::__construct(sprintf('No offers for lead [%s]', $lead->getIdentifier()));
    }
}
