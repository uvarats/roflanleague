<?php

namespace App\Entity;

enum TournamentType: string
{
    case ROUND_ROBIN = 'round robin';
    case DOUBLE_ELIMINATION = 'double elimination';
    case SINGLE_ELIMINATION = 'single elimination';

    public function settings(): array
    {
        return match ($this) {
            self::DOUBLE_ELIMINATION => [],
            self::ROUND_ROBIN => [],
            self::SINGLE_ELIMINATION => [],
        };
    }
}
