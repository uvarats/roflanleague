<?php

declare(strict_types=1);

namespace App\Dto;

use App\Dto\Interface\JsonDTOInterface;

class MatchResultDTO implements JsonDTOInterface
{
    public ?int $matchId = null;

    public ?int $tourneyId = null;

    public ?int $homePlayerId = null;

    public ?int $awayPlayerId = null;

    public ?float $homePlayerOdds = null;

    public ?float $awayPlayerOdds = null;

    public ?string $result = null;
}