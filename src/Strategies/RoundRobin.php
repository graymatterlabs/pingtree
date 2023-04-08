<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;
use Psr\SimpleCache\CacheInterface;

class RoundRobin implements Strategy
{
    protected array $pings = [];

    public function __construct(protected CacheInterface $cache)
    {
    }

    public function execute(Lead $lead, array $offers): Offer
    {
        $priority = $this->getPriorityOffers($lead, $offers);

        if (empty($priority)) {
            $this->reset($lead);
            $priority = $offers;
        }

        $offer = array_reduce($priority, function (?Offer $previous, Offer $offer) use ($lead) {
            if (! isset($this->pings[$offer->getIdentifier()])) {
                $this->pings[$offer->getIdentifier()] = $offer->ping($lead);
            }

            if (is_null($previous)) {
                return $offer;
            }

            return $this->pings[$previous->getIdentifier()] > $this->pings[$offer->getIdentifier()]
                ? $previous
                : $offer;
        });

        $this->deprioritizeOfferForLead($lead, $offer);

        return $offer;
    }

    protected function reset(Lead $lead): void
    {
        $this->cache->delete($this->getCacheKey($lead));
    }

    protected function getCacheKey(Lead $lead): string
    {
        return sprintf('round-robin:%s', $lead->getIdentifier());
    }

    protected function getPriorityOffers(Lead $lead, array $offers): array
    {
        $seen = $this->cache->get($this->getCacheKey($lead), []);

        return array_filter($offers, function (Offer $offer) use ($seen) {
            return ! in_array($offer->getIdentifier(), $seen, false);
        });
    }

    public function deprioritizeOfferForLead(Lead $lead, Offer $offer): void
    {
        $key = $this->getCacheKey($lead);

        $seen = $this->cache->get($key, []);

        $seen[] = $offer->getIdentifier();

        $this->cache->set($key, array_unique($seen));
    }
}
