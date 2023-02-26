<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto;

class Links extends AbstractData
{
    public function __construct(
        public readonly ?string $self = null,
    )
    {
    }
}