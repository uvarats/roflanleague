<?php

namespace App\Entity;

use App\Repository\TourneyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourneyRepository::class)]
class Tourney
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'tourney', targetEntity: Season::class)]
    private Collection $seasons;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tourneys')]
    private Collection $participants;

    #[ORM\Column]
    private ?float $impactCoefficient = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Season $currentSeason = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->seasons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setTourney($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getTourney() === $this) {
                $season->setTourney(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getImpactCoefficient(): ?float
    {
        return $this->impactCoefficient;
    }

    public function setImpactCoefficient(float $impactCoefficient): self
    {
        $this->impactCoefficient = $impactCoefficient;

        return $this;
    }

    public function getCurrentSeason(): ?Season
    {
        return $this->currentSeason;
    }

    public function setCurrentSeason(?Season $currentSeason): self
    {
        $this->currentSeason = $currentSeason;

        return $this;
    }
}
