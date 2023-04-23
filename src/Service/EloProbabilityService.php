<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\OutcomesProbability;

class EloProbabilityService
{
    public function calcsulate(int $firstRating, int $secondRating): OutcomesProbability
    {
        return new OutcomesProbability(
            firstWinProbability: $this->winProbability($firstRating, $secondRating),
            secondWinProbability: $this->winProbability($secondRating, $firstRating),
            tieProbability: $this->tieProbability($firstRating, $secondRating),
        );
    }

    private function winProbability(int $a, int $b): float
    {
        return 1 / (1.0 + pow(10, ($b - $a) / 400.0));
    }

    private function tieProbability(int $a, int $b): float
    {
        return $this->winProbability($a, $b) + $this->winProbability($b, $a);
    }
}