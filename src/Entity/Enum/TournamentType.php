<?php

namespace App\Entity\Enum;

enum TournamentType: string
{
    //case ROUND_ROBIN = 'round robin';
    case DOUBLE_ELIMINATION = 'double elimination';
    case SINGLE_ELIMINATION = 'single elimination';
}
