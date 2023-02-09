<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class ConstructorMissingException extends \Exception
{

    public function __construct(
        string $message = "Requested class does not have constructor!",
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}