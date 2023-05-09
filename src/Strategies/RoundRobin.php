<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

final class RoundRobin implements Strategy
{
    public function __construct(private CacheInterface $cache, private Strategy $strategy)
    {
    }

    /**
     * @param array<Offer> $offers
     *
     * @throws InvalidArgumentException
     */
    public function execute(Lead $lead, array $offers): Offer
    {
        $priority = $this->getPriorityOffers($lead, $offers);

        if (empty($priority)) {
            $this->reset($lead);

            $priority = $offers;
        }

        $offer = $this->strategy->execute($lead, $priority);

        $this->deprioritizeOfferForLead($lead, $offer);

        return $offer;
    }

    public function deprioritizeOfferForLead(Lead $lead, Offer $offer): void
    {
        $key = $this->getCacheKey($lead);

        $seen = (array) $this->cache->get($key);

        $this->cache->set($key, array_unique([...$seen, $offer->getIdentifier()]));
    }

    private function reset(Lead $lead): void
    {
        $this->cache->delete($this->getCacheKey($lead));
    }

    private function getCacheKey(Lead $lead): string
    {
        return sprintf('round-robin:%s', $lead->getIdentifier());
    }

    /**
     * @param array<Offer> $offers
     *
     * @return array<Offer>
     *
     * @throws InvalidArgumentException
     */
    private function getPriorityOffers(Lead $lead, array $offers): array
    {
        $seen = (array) $this->cache->get($this->getCacheKey($lead));

        return array_filter($offers, static function (Offer $offer) use ($seen) {
            return ! in_array($offer->getIdentifier(), $seen, false);
        });
    }
}
