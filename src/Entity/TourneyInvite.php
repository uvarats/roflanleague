<?php

namespace App\Entity;

use App\Repository\TourneyInviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourneyInviteRepository::class)]
class TourneyInvite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $slug = null;

    #[ORM\OneToOne(inversedBy: 'invite', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tourney $tourney = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTourney(): ?Tourney
    {
        return $this->tourney;
    }

    public function setTourney(Tourney $tourney): self
    {
        $this->tourney = $tourney;

        return $this;
    }
}
