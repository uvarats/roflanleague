<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Enum\Result;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class RatingCalculationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RatingService $ratingService,
    ) {
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
                $result = 1;
                $exponent = ($loserRating - $winnerRating) / 400.0;
                $expectedResult = $this->getExpectedResult($exponent);
                $ratingDifferenceSum += 50 * ($result - $expectedResult);
                continue;
            }

            if ($result->getResult() === $loseResult) {
                $result = 0;
                $exponent = ($winnerRating - $loserRating) / 400.0;
                $expectedResult = $this->getExpectedResult($exponent);

                $ratingDifferenceSum += 50 * ($result - $expectedResult);
            }
        }

        return $ratingDifferenceSum;
    }

    private function getExpectedResult(float $exponent): float
    {
        return 1.0 / (1.0 + pow(10, $exponent));
    }

    private function applyDifferences(array $participants, array $differences): void
    {
        for ($i = 0; $i < count($participants); $i++) {
            /** @var User $participant */
            $participant = $participants[$i];
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
