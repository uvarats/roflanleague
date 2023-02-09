<?php

declare(strict_types=1);

namespace App\Dto;

use App\Exception\Challonge\InvalidCodeException;

final readonly class ChallongeCodeDTO
{
    public function __construct(
        public string $code
    )
    {
    }

    public static function fromCode(?string $code): ChallongeCodeDTO
    {
        if ($code === null) {
            throw new InvalidCodeException();
        }

        return new self($code);
    }
}