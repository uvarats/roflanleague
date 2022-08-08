<?php

namespace App\Service;

use Reflex\Challonge\Challonge;

class ChallongeService
{
    private Challonge $challonge;

    public function __construct(string $apiToken)
    {
        $http = new \GuzzleHttp\Client();
        $this->challonge = new Challonge($http, $apiToken, true);
    }

    public function getChallonge(): Challonge
    {
        return $this->challonge;
    }
}