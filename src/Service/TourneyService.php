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
        private RatingCalculationService $calculationService,
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
        $this->calculationService->recalculateRating($tourney);
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