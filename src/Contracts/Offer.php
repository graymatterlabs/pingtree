<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Offer
{
    /**
     * Get the offer's unique identifier.
     */
    public function getIdentifier(): string|int;

    /**
     * Ping the offer returning a score for the lead.
     */
    public function ping(Lead $lead): int;

    /**
     * Send the lead to the offer.
     */
    public function send(Lead $lead): Response;

    /**
     * Whether the lead is eligible for the offer.
     */
    public function isEligible(Lead $lead): bool;

    /**
     * Whether the offer is healthy.
     */
    public function isHealthy(): bool;
}
