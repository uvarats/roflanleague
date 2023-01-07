<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\MatchOdds;
use App\Entity\Odds;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Reflex\Challonge\DTO\MatchDto;
use Reflex\Challonge\DTO\Participant;
use Symfony\Component\Yaml\Dumper;
use function Amp\call;

class OddsService
{
    public function __construct(
        private readonly float         $marginPercent,
        private ChallongeService       $challonge,
        private EntityManagerInterface $em
    )
    {
    }

    public function calculate(User $firstUser, User $secondUser): Odds
    {
        $firstRating = $firstUser->getRating();
        $secondRating = $secondUser->getRating();

        $allOutcomes = $this->applyMargin($firstRating + $secondRating);
        $firstOdds = round(1 / ($firstRating / $allOutcomes), 2);
        $secondOdds = round(1 / ($secondRating / $allOutcomes), 2);

        return Odds::create($firstOdds, $secondOdds);
    }

    /**
     * @param Collection $matches
     * @param Tourney $tourney
     * @return Collection<MatchOdds>
     */
    public function getMatchesOdds(Collection $matches, Tourney $tourney): Collection
    {
        $users = $this->em->getRepository(User::class);
        $participants = $this->challonge->getParticipants($tourney);
        $findCallback = static function (int $id, Collection $participants) {
            return $participants->first(static function (Participant $participant) use ($id) {
                return $participant->id === $id;
            });
        };
        return $matches->transform(function (MatchDto $match) use ($users, $participants, $findCallback) {
            /** @var Participant $firstPlayer */
            $firstPlayer = call_user_func($findCallback, $match->player1_id, $participants);
            /** @var Participant $secondPlayer */
            $secondPlayer = call_user_func($findCallback, $match->player2_id, $participants);

            $firstUser = $users->findOneBy(['username' => $firstPlayer->name]);
            $secondUser = $users->findOneBy(['username' => $secondPlayer->name]);

            $odds = $this->calculate($firstUser, $secondUser);

            return MatchOdds::create()
                ->setMatch($match)
                ->setFirstPlayer($firstUser)
                ->setSecondPlayer($secondUser)
                ->setOdds($odds);
        });
    }

    private function applyMargin(float $outcomes): float
    {
        $margin = $this->marginPercent / 100;
        return $outcomes + $outcomes * $margin;
    }

}