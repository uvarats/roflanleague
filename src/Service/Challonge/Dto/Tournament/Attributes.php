<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto\Tournament;

use App\Service\Challonge\Dto\AbstractData;

class Attributes extends AbstractData
{
    public function __construct(
        public readonly string      $url,
        public readonly string      $name,
        public readonly string      $state,
        public readonly ?bool       $private = null,
        public readonly ?string     $description = null,
        public readonly ?string     $gameName = null,
        public readonly ?string     $fullChallongeUrl = null,
        public readonly ?string     $liveImageUrl = null,
        public readonly ?string     $signUpUrl = null,
        public readonly ?string     $tournamentType = null,
        public readonly ?bool       $notifyUponTournamentEnds = null,
        public readonly ?bool       $notifyUponMatchesOpen = null,
        public readonly ?bool       $acceptAttachments = null,
        public readonly ?bool       $openSignup = null,
        public readonly ?bool       $hideSeeds = null,
        public readonly ?bool       $sequentialPairings = null,
        public readonly ?int        $roundRobinIterations = null,
        public readonly ?string     $rankedBy = null,
        public readonly ?string     $ptsForGameWin = null,
        public readonly ?string     $ptsForGameTie = null,
        public readonly ?string     $ptsForMatchWin = null,
        public readonly ?string     $ptsForMatchTie = null,
        public readonly ?Timestamps $timestamps = null,
    )
    {
    }
}