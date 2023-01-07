<?php

declare(strict_types=1);

namespace App\Entity;

use JetBrains\PhpStorm\Pure;
use Reflex\Challonge\DTO\MatchDto;

class MatchOdds
{
    private MatchDto $match;

    private User $firstPlayer;

    private User $secondPlayer;

    private Odds $odds;

    #[Pure]
    public static function create(): MatchOdds
    {
        return new self();
    }


    /**
     * @param User $firstPlayer
     * @return MatchOdds
     */
    public function setFirstPlayer(User $firstPlayer): MatchOdds
    {
        $this->firstPlayer = $firstPlayer;
        return $this;
    }

    /**
     * @return User
     */
    public function getFirstPlayer(): User
    {
        return $this->firstPlayer;
    }

    /**
     * @return MatchDto
     */
    public function getMatch(): MatchDto
    {
        return $this->match;
    }

    /**
     * @param MatchDto $match
     * @return MatchOdds
     */
    public function setMatch(MatchDto $match): MatchOdds
    {
        $this->match = $match;
        return $this;
    }

    /**
     * @return User
     */
    public function getSecondPlayer(): User
    {
        return $this->secondPlayer;
    }

    /**
     * @param User $secondPlayer
     * @return MatchOdds
     */
    public function setSecondPlayer(User $secondPlayer): MatchOdds
    {
        $this->secondPlayer = $secondPlayer;
        return $this;
    }

    /**
     * @return Odds
     */
    public function getOdds(): Odds
    {
        return $this->odds;
    }

    /**
     * @param Odds $odds
     * @return MatchOdds
     */
    public function setOdds(Odds $odds): MatchOdds
    {
        $this->odds = $odds;
        return $this;
    }
}