<?php

declare(strict_types=1);

namespace App\Exception;

use App\Entity\Enum\Result;
use App\Entity\MatchResult;

class InvalidMatchResultException extends \LogicException
{
    private MatchResult $result;

    public function __construct(MatchResult $result)
    {
        $resultEnum = $result->getResult();
        $message = "Invalid match result!";

        if ($resultEnum === Result::CANCELED || !$resultEnum instanceof \App\Entity\Enum\Result) {
            $message = "Result cannot be cancelled or null!";
        }

        parent::__construct($message);
    }

    /**
     * @return MatchResult
     */
    public function getResult(): MatchResult
    {
        return $this->result;
    }
}