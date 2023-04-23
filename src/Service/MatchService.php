<?php

namespace App\Service;

use App\Dto\MatchResultDTO;
use App\Entity\Enum\Result;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Random\Randomizer;

final readonly class MatchService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ChallongeService       $challonge
    )
    {
    }

    private function loadResults(MatchResultDTO $data): MatchResult
    {
        $result = new MatchResult();
        $result->setChallongeMatchId($data->matchId);

        $tourney = $this->getTourney($data->tourneyId);
        $homePlayer = $this->getUser($data->homePlayerId);
        $awayPlayer = $this->getUser($data->awayPlayerId);

        $result->setTourney($tourney)
            ->setHomePlayer($homePlayer)
            ->setAwayPlayer($awayPlayer)
            ->setHomePlayerOdds($data->homePlayerOdds)
            ->setAwayPlayerOdds($data->awayPlayerOdds);

        if ($data->result !== null) {
            $result->setResult(Result::from($data->result));
        }

        return $result;
    }

    public function saveResult(MatchResultDTO $result): void
    {
        $matchResult = $this->loadResults($result);

        $this->sendResultToChallonge($matchResult);
    }

    public function randomResult(MatchResultDTO $result): void
    {
        $homePlayer = $this->getUser($result->homePlayerId);
        $awayPlayer = $this->getUser($result->awayPlayerId);

        $matchResult = $this->loadResults($result);
        $resultEnum = $this->getRandomWinner($homePlayer, $awayPlayer);
        $matchResult->setResult($resultEnum);

        $this->sendResultToChallonge($matchResult);
    }

    private function sendResultToChallonge(MatchResult $result): void
    {
        $this->challonge->setMatchResult($result);
        $result->setFinishedAt(new \DateTimeImmutable());

        $this->em->persist($result);
        $this->em->flush();
    }

    private function getRandomWinner(User $homePlayer, User $awayPlayer): Result
    {
        $homePlayerRating = $homePlayer->getRating();
        $awayPlayerRating = $awayPlayer->getRating();

        $allOutcomes = $homePlayerRating + $awayPlayerRating;

        $randomizer = new Randomizer();
        $result = $randomizer->getInt(1, $allOutcomes);

        if ($result <= $homePlayerRating) {
            return Result::FIRST_WIN;
        }

        return Result::SECOND_WIN;
    }

    private function getTourney(string|int $id) {
        $tourneys = $this->em->getRepository(Tourney::class);
        return $tourneys->findOneBy(['id' => $id]);
    }

    private function getUser(string|int $id) {
        $users = $this->em->getRepository(User::class);
        return $users->findOneBy(['id' => $id]);
    }
}