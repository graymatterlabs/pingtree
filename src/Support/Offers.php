<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Support;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;

final class Offers
{
    /** @var array<Offer> */
    private array $offers;

    public function __construct(Offer ...$offers)
    {
        $this->offers = $offers;
    }

    /**
     * @param array<Offer> $offers
     */
    public static function wrap(array $offers): Offers
    {
        return new self(...$offers);
    }

    public function healthy(): Offers
    {
        return self::wrap(array_filter($this->offers, static fn (Offer $offer) => $offer->isHealthy()));
    }

    public function eligible(Lead $lead): Offers
    {
        return self::wrap(array_filter($this->offers, static fn (Offer $offer) => $offer->isEligible($lead)));
    }

    /**
     * @param array<Offer> $offers
     */
    public function except(array $offers): Offers
    {
        $identifiers = self::wrap($offers)->toIdentifiers();

        return self::wrap(array_filter($this->offers, static fn (Offer $offer) => ! in_array($offer->getIdentifier(), $identifiers, true)));
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

    /**
     * @return array<string|int>
     */
    public function toIdentifiers(): array
    {
        return array_map(static fn (Offer $offer) => $offer->getIdentifier(), $this->offers);
    }

    /**
     * @return array<Offer>
     */
    public function toArray(): array
    {
        return $this->offers;
    }
}
