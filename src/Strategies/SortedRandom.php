<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Strategies;

use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Strategy;

class SortedRandom implements Strategy
{
    /**
     * @var array<int, Offer[]>
     */
    protected array $scores = [];

    public function execute(Lead $lead, array $offers): Offer
    {
        foreach ($offers as $offer) {
            $this->score($offer, $offer->ping($lead));
        }

        ksort($this->scores);

        $key = array_key_last($this->scores);

        return $this->scores[$key][array_rand($this->scores[$key])];
    }

    protected function score(Offer $offer, int $score): void
    {
        $this->scores[$score][] = $offer;
    }
}
