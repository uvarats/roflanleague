<?php

declare(strict_types=1);

namespace App\Entity;

use JetBrains\PhpStorm\Pure;
use Reflex\Challonge\DTO\MatchDto;

class MatchOdds
{
    private MatchDto $match;
    private Odds $odds;

    #[Pure]
    public static function create(MatchDto $match, Odds $odds): MatchOdds
    {
        $matchOdds = new self();
        $matchOdds->match = $match;
        $matchOdds->odds = $odds;
        return $matchOdds;
    }

    /**
     * @return MatchDto
     */
    public function getMatch(): MatchDto
    {
        return $this->match;
    }

    /**
     * @return Odds
     */
    public function getOdds(): Odds
    {
        return $this->odds;
    }
}