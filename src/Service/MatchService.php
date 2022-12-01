<?php

namespace App\Service;

use App\Entity\Enum\Result;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MatchService
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function loadResults(array $data): MatchResult
    {
        $result = new MatchResult();
        $result->setChallongeMatchId((int)$data['matchId']);

        $tourney = $this->getTourney((int)$data['tourneyId']);
        $homePlayer = $this->getUser($data['homePlayerId']);
        $awayPlayer = $this->getUser($data['awayPlayerId']);

        $result->setTourney($tourney)
            ->setHomePlayer($homePlayer)
            ->setAwayPlayer($awayPlayer)
            ->setHomePlayerOdds((float)$data['homePlayerOdds'])
            ->setAwayPlayerOdds((float)$data['awayPlayerOdds']);

        if (isset($data['result'])) {
            $result->setResult(Result::from($data['result']));
        }

        return $result;
    }

    public function calculateRating(MatchResult $result) {

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