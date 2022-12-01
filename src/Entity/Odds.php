<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Здесь хранятся коэффициенты на участников матча (с учётом маржи)
 */
class Odds
{
    private float $homeOdds;
    private float $awayOdds;

    public static function create(
        float $firstOdds, float $secondOdds
    ): Odds
    {
        $odds = new self();
        $odds->homeOdds = $firstOdds;
        $odds->awayOdds = $secondOdds;
        return $odds;
    }

    /**
     * @return float
     */
    public function getHomeOdds(): float
    {
        return $this->homeOdds;
    }

    /**
     * @return float
     */
    public function getAwayOdds(): float
    {
        return $this->awayOdds;
    }

}