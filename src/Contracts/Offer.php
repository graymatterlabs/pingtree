<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Contracts;

interface Offer
{
    /**
     * Get the offers unique identifier.
     *
     * @return string|int
     */
    public function getIdentifier(): string|int;

    /**
     * Ping the offer returning a score for the lead.
     *
     * @param \GrayMatterLabs\PingTree\Contracts\Lead $lead
     *
     * @return int
     */
    public function ping(Lead $lead): int;

    /**
     * Send the lead to the offer.
     *
     * @param \GrayMatterLabs\PingTree\Contracts\Lead $lead
     *
     * @return \GrayMatterLabs\PingTree\Contracts\Response
     */
    public function send(Lead $lead): Response;

    /**
     * Baseline check whether the lead is eligible for the offer.
     *
     * @param \GrayMatterLabs\PingTree\Contracts\Lead $lead
     *
     * @return bool
     */
    public function isEligible(Lead $lead): bool;

    /**
     * Whether the offer is healthy.
     *
     * @return bool
     */
    public function isHealthy(): bool;

    /**
     * Notify the offer that it has failed to receive a lead.
     *
     * @param \GrayMatterLabs\PingTree\Contracts\Lead $lead
     * @param \GrayMatterLabs\PingTree\Contracts\Response $response
     *
     * @return void
     */
    public function notifyOfFailure(Lead $lead, Response $response): void;
}
