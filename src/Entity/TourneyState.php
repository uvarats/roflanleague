<?php

namespace App\Entity;

enum TourneyState: string
{
    case NEW = 'new';
    case STARTED = 'started';
    case ENDED = 'ended';
}
