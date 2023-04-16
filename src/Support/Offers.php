<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;

final class Offers
{
    private array $offers;

    public function __construct(Offer ...$offers)
    {
        $this->offers = $offers;
    }

    public static function wrap(array $offers): Offers
    {
        return new self(...$offers);
    }

    public function healthy(): Offers
    {
        return self::wrap(array_filter($this->offers, fn ($offer) => $offer->isHealthy()));
    }

    public function eligible(Lead $lead): Offers
    {
        return self::wrap(array_filter($this->offers, fn ($offer) => $offer->isEligible($lead)));
    }

    public function except(array $offers): Offers
    {
        $identifiers = self::wrap($offers)->toIdentifiers();

        return self::wrap(array_filter($this->offers, fn ($offer) => ! in_array($offer->getIdentifier(), $identifiers, true)));
    }

    public function unique(): Offers
    {
        $offers = [];

        foreach ($this->offers as $offer) {
            $offers[$offer->getIdentifier()] = $offer;
        }

        return self::wrap(array_values($offers));
    }

    public function isEmpty(): bool
    {
        return empty($this->offers);
    }

    public function toIdentifiers(): array
    {
        return array_map(fn (Offer $offer) => $offer->getIdentifier(), $this->offers);
    }

    public function toArray(): array
    {
        return $this->offers;
    }
}
