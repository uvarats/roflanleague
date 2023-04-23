<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class OutcomesProbability
{
    public function __construct(
        public float $firstWinProbability,
        public float $secondWinProbability,
    )
    {
    }
}