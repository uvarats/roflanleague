<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Enum\Result;
use App\Entity\Enum\TournamentType;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use App\Exception\InvalidMatchResultException;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Reflex\Challonge\Challonge;
use Reflex\Challonge\DTO\MatchDto;
use Reflex\Challonge\DTO\Participant;
use Reflex\Challonge\DTO\Tournament;

/**
 * Soon will be deprecated.
 * Challonge API v2 in dev.
 */
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

    public function getTournaments(): Collection
    {
        return $this->challonge->getTournaments();
    }

    public function createTournament(string $name, TournamentType $type): Tournament
    {
        return $this->challonge->createTournament([
            'name' => $name,
            'tournament_type' => $type->value,
        ]);
    }

    public function addParticipant(Tourney $tourney, User $user): Participant
    {
        return $this->fetchTournament($tourney)
            ->addParticipant([
                'name' => $user->getUsername(),
                'misc' => $user->getId(),
            ]);
    }

    public function removeParticipant(Tourney $tourney, User $user): bool
    {
        $participants = $this->challonge->getParticipants($tourney->getChallongeUrl());
        $username = $user->getUsername();

        $participant = $this->findParticipant(static function (Participant $participant) use ($username) {
            return $participant->name === $username;
        }, $participants);

        return (bool)$participant->delete();
    }

    public function fetchTournament(
        Tourney $tourney,
    ): Tournament
    {
        return $this->challonge->fetchTournament($tourney->getChallongeUrl());
    }

    public function fetchTournamentById(string $challongeId): Tournament
    {
        return $this->challonge->fetchTournament($challongeId);
    }

    public function startTournament(Tourney $tourney): Tournament
    {
        return $this->fetchTournament($tourney)->start();
    }

    public function resetTournament(Tourney $tourney): Tournament
    {
        return $this->fetchTournament($tourney)->reset();
    }

    public function endTournament(Tourney $tourney): Tournament
    {
        return $this->fetchTournament($tourney)
            ->finalize();
    }

    public function randomizeParticipants(Tourney $tourney): void
    {
        $this->getChallonge()
            ->randomizeParticipants($tourney->getChallongeUrl());
    }

    public function removeTournament(string $tourney): void
    {
        $this->challonge->deleteTournament($tourney);
    }

    public function getMatches(Tourney $tourney, string $state = 'open'): Collection
    {
        $matches = $this->challonge->getMatches($tourney->getChallongeUrl());
        return $matches->filter(static function (MatchDto $match) use ($state) {
            return ($match->player1_id != null && $match->player2_id != null) && $match->state === $state;
        });
    }

    public function getParticipant(string|int|Tourney $tourney, int $participantId): Participant
    {
        if ($tourney instanceof Tourney) {
            $tourney = $tourney->getChallongeUrl();
        }

        return $this->challonge->getParticipant($tourney, $participantId);
    }

    public function getParticipants(Tourney $tourney): Collection
    {
        return $this->challonge->getParticipants($tourney->getChallongeUrl());
    }

    public function getParticipantsByUrl(string $url): Collection
    {
        return $this->challonge->getParticipants($url);
    }

    /**
     * @param callable $condition
     * @param Collection<Participant> $participants
     * @return Participant
     */
    public function findParticipant(callable $condition, Collection $participants): Participant
    {
        return $participants->first($condition);
    }

    public function setMatchResult(MatchResult $result): MatchDto
    {
        $tourney = $result->getTourney();
        $challongeTourney = $tourney->getChallongeUrl();
        $matchId = $result->getChallongeMatchId();
        $match = $this->challonge->getMatch($challongeTourney, $matchId);

        $resultEnum = $result->getResult();

        $winnerId = match ($resultEnum) {
            Result::FIRST_WIN => $match->player1_id,
            Result::SECOND_WIN => $match->player2_id,
            Result::TIE => 'tie',
            Result::CANCELED, null => throw new InvalidMatchResultException($result),
        };

        return $match->update([
            'winner_id' => $winnerId,
            'scores_csv' => $this->getScoreForResult($resultEnum),
        ]);
    }

    private function getScoreForResult(Result $result): string
    {
        return match ($result) {
            Result::FIRST_WIN => '1-0',
            Result::SECOND_WIN => '0-1',
            Result::TIE, Result::CANCELED => '0-0',
        };
    }
}