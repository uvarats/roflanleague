<?php

declare(strict_types=1);

namespace App\Exception\Challonge;

use Throwable;

class UserMissingTokenException extends \Exception
{
    public function __construct(
        string $message = "Подключите аккаунт Challonge, чтобы совершить данное действие!",
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}