<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;

final readonly class TourneyDatesDto
{
    public function __construct(
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
    )
    {
    }
}