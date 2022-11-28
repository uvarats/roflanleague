<?php

namespace App\Entity\Enum;

enum TourneyState: string
{
    case NEW = 'new';
    case STARTED = 'started';
    case ENDED = 'ended';
}
