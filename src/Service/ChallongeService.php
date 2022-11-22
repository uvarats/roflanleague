<?php

namespace App\Service;

use App\Entity\TournamentType;
use App\Entity\User;
use GuzzleHttp\Client;
use Reflex\Challonge\Challonge;
use Reflex\Challonge\DTO\Participant;
use Reflex\Challonge\DTO\Tournament;

class ChallongeService
{
    private Challonge $challonge;

    public function __construct(string $apiToken)
    {
        $http = new Client();
        $this->challonge = new Challonge($http, $apiToken, true);
    }

    public function getChallonge(): Challonge
    {
        return $this->challonge;
    }

    public function createTournament(string $name, TournamentType $type): Tournament
    {
        return $this->challonge->createTournament([
            'name' => $name,
            'tournament_type' => $type->value,
        ]);
    }

    public function addParticipant(string|int $tourney, User $user): Participant
    {
        return $this->fetchTournament($tourney)->addParticipant([
            'name' => $user->getUsername(),
            'misc' => $user->getId(),
        ]);
    }

    public function removeParticipant(string|int $tourney, User $user): bool
    {
        $tournament = $this->fetchTournament($tourney);
        $iterator = $this->challonge->getParticipants($tourney)->getIterator();
        /** @var Participant $item */
        foreach ($iterator as $item) {
            if ($item->name === $user->getUsername()) {
                $tournament->deleteParticipant($item->id);
                return true;
            }
        }
        return false;
    }

    public function removeTournament(string|int $idOrUrl)
    {
        $this->challonge->deleteTournament($idOrUrl);
    }

    public function fetchTournament(string|int $tournament): Tournament
    {
        return $this->challonge->fetchTournament($tournament);
    }

}