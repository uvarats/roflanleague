<?php

namespace App\Service;

use GuzzleHttp\Client;
use Reflex\Challonge\Challonge;
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

    public function createTournament(string $name, string $type): Tournament
    {
        return $this->challonge->createTournament([
            'name' => $name,
            'tournament_type' => $type,
        ]);
    }

    public function fetchTournament(string $tournament): Tournament
    {
        return $this->challonge->fetchTournament($tournament);
    }
}