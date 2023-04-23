<?php

declare(strict_types=1);

namespace App\Dto;


use Uvarats\Dto\Data;

/**
 * TODO: check this with new json mapper. Test match result setting.
 */
class MatchResultDTO extends Data
{
    public function __construct(
        public readonly ?int    $matchId = null,
        public readonly ?int    $tourneyId = null,
        public readonly ?int    $homePlayerId = null,
        public readonly ?int    $awayPlayerId = null,
        public readonly ?float  $homePlayerOdds = null,
        public readonly ?float  $awayPlayerOdds = null,
        public readonly ?string $result = null,
    )
    {

    }
}