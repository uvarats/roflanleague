<?php

namespace App\Service;

use App\Entity\TournamentType;
use App\Entity\Tourney;
use App\Entity\User;
use GuzzleHttp\Client;
use Reflex\Challonge\Challonge;
use Reflex\Challonge\DTO\Participant;
use Reflex\Challonge\DTO\Tournament;
use Reflex\Challonge\Exceptions\AlreadyStartedException;
use Reflex\Challonge\Exceptions\InvalidFormatException;
use Reflex\Challonge\Exceptions\NotFoundException;
use Reflex\Challonge\Exceptions\ServerException;
use Reflex\Challonge\Exceptions\StillRunningException;
use Reflex\Challonge\Exceptions\UnauthorizedException;
use Reflex\Challonge\Exceptions\UnexpectedErrorException;
use Reflex\Challonge\Exceptions\ValidationException;

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

    /**
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws ServerException
     * @throws ValidationException
     * @throws InvalidFormatException
     * @throws \JsonException
     * @throws UnauthorizedException
     */
    public function createTournament(string $name, TournamentType $type): Tournament
    {
        return $this->challonge->createTournament([
            'name' => $name,
            'tournament_type' => $type->value,
        ]);
    }

    /**
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws ServerException
     * @throws InvalidFormatException
     * @throws ValidationException
     * @throws \JsonException
     * @throws UnauthorizedException
     */
    public function addParticipant(Tourney $tourney, User $user): Participant
    {
        return $this->fetchTournament($tourney)
            ->addParticipant([
                'name' => $user->getUsername(),
                'misc' => $user->getId(),
            ]);
    }

    /**
     * @throws ValidationException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws ServerException
     * @throws InvalidFormatException
     * @throws \JsonException
     */
    public function removeParticipant(Tourney $tourney, User $user): bool
    {
        $tournament = $this->fetchTournament($tourney);
        $iterator = $this->challonge->getParticipants($tourney->getChallongeId())->getIterator();
        /** @var Participant $item */
        foreach ($iterator as $item) {
            if ($item->name === $user->getUsername()) {
                $tournament->deleteParticipant($item->id);
                return true;
            }
        }
        return false;
    }

    /**
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws ServerException
     * @throws ValidationException
     * @throws InvalidFormatException
     * @throws \JsonException
     * @throws UnauthorizedException
     * @throws AlreadyStartedException
     */
    public function startTournament(Tourney $tourney): Tournament
    {
        return $this->getChallonge()
            ->fetchTournament($tourney->getChallongeId())
            ->start();
    }

    /**
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws StillRunningException
     * @throws ServerException
     * @throws InvalidFormatException
     * @throws ValidationException
     * @throws \JsonException
     * @throws UnauthorizedException
     */
    public function endTournament(Tourney $tourney): Tournament
    {
        return $this->getChallonge()
            ->fetchTournament($tourney->getChallongeId())
            ->finalize();
    }

    /**
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws ServerException
     * @throws InvalidFormatException
     * @throws ValidationException
     * @throws \JsonException
     * @throws UnauthorizedException
     */
    public function randomizeParticipants(Tourney $tourney): void
    {
        $this->getChallonge()
            ->randomizeParticipants($tourney->getChallongeId());
    }

    /**
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws ServerException
     * @throws InvalidFormatException
     * @throws ValidationException
     * @throws \JsonException
     * @throws UnauthorizedException
     */
    public function removeTournament(string $tourney): void
    {
        $this->challonge->deleteTournament($tourney);
    }

    /**
     * @throws UnexpectedErrorException
     * @throws NotFoundException
     * @throws ServerException
     * @throws ValidationException
     * @throws InvalidFormatException
     * @throws \JsonException
     * @throws UnauthorizedException
     */
    public function fetchTournament(Tourney $tourney): Tournament
    {
        return $this->challonge->fetchTournament($tourney->getChallongeId());
    }

}