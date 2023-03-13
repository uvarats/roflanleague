<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum ChallongeTourneyState: string
{
    case PENDING = 'pending';
    case COMPLETE = 'complete';
}