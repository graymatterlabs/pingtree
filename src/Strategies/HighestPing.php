<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;
use GrayMatterLabs\SimpleCache\ArrayCache;
use Psr\SimpleCache\CacheInterface;

class HighestPing implements Strategy
{
    protected CacheInterface $cache;

    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache ?? new ArrayCache();
    }

    public function execute(Lead $lead, array $offers): Offer
    {
        return array_reduce($offers, function (?Offer $previous, Offer $offer) use ($lead) {
            if (is_null($previous)) {
                $this->ping($lead, $offer);

                return $offer;
            }

            return $this->ping($lead, $previous) > $this->ping($lead, $offer)
                ? $previous
                : $offer;
        });
    }

    protected function ping(Lead $lead, Offer $offer): int
    {
        $key = sprintf('ping:%s:%s', $lead->getIdentifier(), $offer->getIdentifier());

        if (is_null($ping = $this->cache->get($key))) {
            $this->cache->set($key, $ping = $offer->ping($lead));
        }

        return $ping;
    }
}
