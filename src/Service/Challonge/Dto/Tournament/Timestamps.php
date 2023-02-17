<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto\Tournament;

use App\Service\Challonge\Dto\AbstractData;

class Timestamps extends AbstractData
{
    public function __construct(
        public readonly ?string $startsAt = null,
        public readonly ?string $startedAt = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $completedAt = null,
    )
    {
    }
}