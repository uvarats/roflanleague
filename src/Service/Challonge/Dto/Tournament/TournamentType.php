<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto\Tournament;

enum TournamentType: string
{
    case SINGLE_ELIMINATION = 'single elimination';
    case DOUBLE_ELIMINATION = 'double elimination';
    case ROUND_ROBIN = 'round robin';
    case SWISS = 'swiss';
    case FREE_FOR_ALL = 'free for all';
}