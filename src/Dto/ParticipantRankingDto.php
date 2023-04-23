<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class ParticipantRankingDto
{
    public function __construct(
        public string $username,
        public int $position,
    )
    {
    }
}