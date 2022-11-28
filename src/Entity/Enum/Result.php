<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum Result: string
{
    case FIRST_WIN = 'w1';
    case SECOND_WIN = 'w2';
    case TIE = 'x';
    case CANCELED = 'cancel';
}