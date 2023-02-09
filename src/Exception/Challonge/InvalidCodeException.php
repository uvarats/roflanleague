<?php

declare(strict_types=1);

namespace App\Exception\Challonge;

use Throwable;

class InvalidCodeException extends \Exception
{
    public function __construct(
        string $message = "Invalid Challonge code",
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}