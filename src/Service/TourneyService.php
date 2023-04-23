<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ParticipantRankingDto;
use App\Dto\TourneyDatesDto;
use App\Entity\Enum\Result;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection as IlluminateCollection;
use Psr\Log\LoggerInterface;
use Reflex\Challonge\DTO\Participant;
use Symfony\Component\Form\FormInterface;

final readonly class TourneyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChallongeService $challonge,
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
    ) {
    }

    public function createTourney(Tourney $tourney, FormInterface $form): ?Tourney
    {
        $challongeTourney = $this->challonge
            ->createTournament($tourney->getName(), $form->get('type')->getNormData());

        $tourney->setChallongeUrl($challongeTourney->url);

        $this->entityManager->persist($tourney);
        try {
            $this->entityManager->flush();
        } catch (\Throwable $throwable) {
            $this->challonge->removeTournament($challongeTourney->url);
            $this->logger->error("Error on tourney create: {$throwable->getMessage()}");
            return null;
        }

        return $tourney;
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

    public function getTourneyDates(Tourney $tourney): TourneyDatesDto
    {
        $tournament = $this->challonge->fetchTournament($tourney);

        $timezone = new \DateTimeZone('Europe/Minsk');
        $startDate = new \DateTimeImmutable($tournament->started_at, $timezone);
        $endDate = new \DateTimeImmutable($tournament->completed_at, $timezone);

        return new TourneyDatesDto(
            startDate: $startDate,
            endDate: $endDate
        );
    }

    /**
     * @return ParticipantRankingDto[]
     */
    public function getFinalRanks(Tourney $tourney): array
    {
        $results = $this->challonge->getParticipants($tourney);

        /** @var IlluminateCollection|ParticipantRankingDto[] $ranks */
        $ranks = $results->map(static function (Participant $participant) {
            return new ParticipantRankingDto(
                username: $participant->name,
                position: $participant->final_rank,
            );
        });

        $ranks = $ranks->sort(static function (ParticipantRankingDto $p1, ParticipantRankingDto $p2) {
            return $p1->position > $p2->position;
        });

        /** @var IlluminateCollection $ranks */
        $ranks = $ranks->values();

        return $ranks->toArray();
    }
}