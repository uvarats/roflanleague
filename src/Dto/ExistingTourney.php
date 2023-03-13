<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ExistingTourney
{
    #[Assert\NotBlank]
    private string $challongeTourneyUrl;

    public function getChallongeTourneyUrl(): string
    {
        return $this->challongeTourneyUrl;
    }

    public function setChallongeTourneyUrl(string $challongeTourneyUrl): void
    {
        $this->challongeTourneyUrl = $challongeTourneyUrl;
    }


}