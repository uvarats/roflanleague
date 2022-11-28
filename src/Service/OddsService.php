<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Odds;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Reflex\Challonge\DTO\MatchDto;

class OddsService
{
    public function __construct(
        private readonly float         $marginPercent,
        private ChallongeService       $challonge,
        private EntityManagerInterface $em
    )
    {
    }

    public function calculate(User $firstUser, User $secondUser): Odds
    {
        $firstRating = $firstUser->getRating();
        $secondRating = $secondUser->getRating();

        $allOutcomes = $this->applyMargin($firstRating + $secondRating);
        $firstOdds = $firstRating / $allOutcomes;
        $secondOdds = $secondRating / $allOutcomes;

        return Odds::create($firstOdds, $secondOdds);
    }

    /**
     * @param Collection<MatchDto> $matches
     * @return Collection
     */
    public function getMatchesOdds(Collection $matches, Tourney $tourney): Collection
    {
        $participants = $this->challonge->getParticipants($tourney->getChallongeUrl());
        return $matches->transform(function (MatchDto $match) {
            $firstPlayer = $this->challonge->getParticipant($match->tournament_id, $match->player1_id);
            $secondPlayer = $this->challonge->getParticipant($match->tournament_id, $match->player2_id);
            return [];
        });
    }

    private function applyMargin(float $outcomes): float
    {
        $margin = $this->marginPercent / 100;
        $outcomes += $outcomes * $margin;
        return $outcomes;
    }

}