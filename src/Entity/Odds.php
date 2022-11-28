<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Здесь хранятся коэффициенты на участников матча (с учётом маржи)
 */
class Odds
{
    private float $firstOdds;
    private float $secondOdds;

    public static function create(
        float $firstOdds, float $secondOdds
    ): Odds
    {
        $odds = new self();
        $odds->firstOdds = $firstOdds;
        $odds->secondOdds = $secondOdds;
        return $odds;
    }

    /**
     * @return float
     */
    public function getFirstOdds(): float
    {
        return $this->firstOdds;
    }

    /**
     * @return float
     */
    public function getSecondOdds(): float
    {
        return $this->secondOdds;
    }

}