<?php

declare(strict_types=1);

namespace App\Exception\Challonge;

use Throwable;

class UnknownTournamentException extends \Exception
{
    public function __construct(
        string $message = "Турнир не найден.",
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}