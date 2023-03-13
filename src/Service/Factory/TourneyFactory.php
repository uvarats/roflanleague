<?php

declare(strict_types=1);

namespace App\Service\Factory;

use App\Dto\ExistingTourney;
use App\Entity\Enum\ChallongeTourneyState;
use App\Exception\Challonge\UnknownTournamentException;
use App\Exception\Challonge\UnsupportedTournamentException;
use App\Service\ChallongeService;
use Reflex\Challonge\DTO\Tournament;

class TourneyFactory
{
    public function __construct(
        private readonly ChallongeService $challonge
    )
    {
    }

    public function createFromChallonge(ExistingTourney $existingTourney)
    {
        $url = $existingTourney->getChallongeTourneyUrl();

        $tourney = $this->findInUserTournaments($url);

        if ($tourney === null) {
            throw new UnknownTournamentException();
        }

        $state = ChallongeTourneyState::from($tourney->state);

        if ($state !== ChallongeTourneyState::PENDING) {
            throw new UnsupportedTournamentException(
                'Поддерживаются только не начатые турниры.'
            );
        }

        if ($tourney->participants_count > 0) {

        }
    }

    private function findInUserTournaments(string $url): ?Tournament
    {
        $tournaments = $this->challonge->getTournaments();

        return $tournaments->first(fn(Tournament $tournament) => $tournament->url === $url);
    }

    private function getUsers(string $url)
    {
        $participants = $this->challonge->getParticipantsByUrl($url);


    }
}