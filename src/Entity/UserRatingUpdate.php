<?php

namespace App\Entity;

use App\Repository\UserRatingUpdateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRatingUpdateRepository::class)]
class UserRatingUpdate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $change = null;

    #[ORM\ManyToOne(inversedBy: 'ratingUpdates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserRating $rating = null;

    #[ORM\ManyToOne(inversedBy: 'ratingUpdates')]
    private ?Tourney $tourney = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChange(): ?int
    {
        return $this->change;
    }

    public function setChange(int $change): self
    {
        $this->change = $change;

        return $this;
    }

    public function getRating(): ?UserRating
    {
        return $this->rating;
    }

    public function setRating(?UserRating $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getTourney(): ?Tourney
    {
        return $this->tourney;
    }

    public function setTourney(?Tourney $tourney): self
    {
        $this->tourney = $tourney;

        return $this;
    }
}
