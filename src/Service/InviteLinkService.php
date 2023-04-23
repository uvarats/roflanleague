<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tourney;
use App\Entity\TourneyInvite;
use App\Repository\TourneyInviteRepository;
use Random\Randomizer;

final readonly class InviteLinkService
{
    public function __construct(private TourneyInviteRepository $inviteRepository)
    {
    }

    public function createInviteLink(Tourney $tourney): TourneyInvite
    {
        $link = new TourneyInvite();
        $slug = $this->generateSlug();

        $link->setSlug($slug)
            ->setTourney($tourney);

        $this->inviteRepository->save($link, true);

        return $link;
    }

    public function regenerate(Tourney $tourney) {

    }

    private function generateSlug(): string
    {
        $randomizer = new Randomizer();
        $randomBytes = $randomizer->getBytes(30);
        $hashed = md5($randomBytes);

        return substr($hashed, 0, 15);
    }
}