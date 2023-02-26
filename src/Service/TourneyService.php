<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Enum\Result;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TourneyService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function recalculateRating(Tourney $tourney): void
    {
        $participants = $tourney->getParticipants();
        $results = $this->em->getRepository(MatchResult::class);

        $ratingDifferences = [];

        foreach ($participants as $participant) {
            $homeResults = $results->findBy([
                'tourney' => $tourney,
                'homePlayer' => $participant,
            ]);

            $awayResults = $results->findBy([
                'tourney' => $tourney,
                'awayPlayer' => $participant
            ]);

            $homeRatingDifference = $this->processHomeResults($homeResults);
            $awayRatingDifference = $this->processAwayResults($awayResults);

            $totalDifference = $homeRatingDifference + $awayRatingDifference;
            $ratingDifferences[] = $totalDifference * $tourney->getImpactCoefficient();
        }

        $this->applyDifferences($participants->toArray(), $ratingDifferences);
        $this->em->flush();
    }

    private function processHomeResults(array $results): float
    {
        return $this->processResults($results, Result::FIRST_WIN, Result::SECOND_WIN);
    }

    private function processAwayResults(array $results): float
    {
        return $this->processResults($results, Result::SECOND_WIN, Result::FIRST_WIN);
    }

    /**
     * @param MatchResult[] $results
     * @param Result $winResult
     * @param Result $loseResult
     * @return float
     */
    private function processResults(array $results, Result $winResult, Result $loseResult): float
    {
        $ratingDifferenceSum = 0.0;

        foreach ($results as $result) {
            $winnerRating = $result->getWinner()->getRating();
            $loserRating = $result->getLoser()->getRating();

            if ($result->getResult() === $winResult) {
                $ratingDifferenceSum += (100 - ($winnerRating - $loserRating)) / 10.0;
                continue;
            }

            if ($result->getResult() === $loseResult) {
                $ratingDifferenceSum += (-1 * (100 - ($winnerRating - $loserRating))) / 20.0;
            }
        }

        return $ratingDifferenceSum;
    }

    private function applyDifferences(array $participants, array $differences): void
    {
        foreach ($participants as $i => $participant) {
            /** @var User $participant */
            $participant = $participant;
            /** @var float $ratingDifference */
            $ratingDifference = $differences[$i];
            $currentRating = $participant->getRating();
            $newRating = $currentRating + $ratingDifference;
            if ($newRating < 10) {
                $newRating = 10;
            }
            $participant->setRating((int)round($newRating));
        }
    }
}