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

    #[ORM\Column]
    private ?float $impactCoefficient = null;

    #[ORM\Column(length: 255)]
    private ?string $challongeUrl = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tourneys')]
    private Collection $participants;

    #[ORM\Column(length: 75, options: ['default' => 'new'])]
    private ?string $state = 'new';

    #[ORM\OneToMany(mappedBy: 'tourney', targetEntity: MatchResult::class)]
    private Collection $matchResults;


    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->matchResults = new ArrayCollection();
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

    public function getChallongeUrl(): ?string
    {
        return $this->challongeUrl;
    }

    public function setChallongeUrl(string $challongeId): self
    {
        $this->challongeUrl = $challongeId;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, MatchResult>
     */
    public function getMatchResults(): Collection
    {
        return $this->matchResults;
    }

    public function addMatchResult(MatchResult $matchResult): self
    {
        if (!$this->matchResults->contains($matchResult)) {
            $this->matchResults->add($matchResult);
            $matchResult->setTourney($this);
        }

        return $this;
    }

    public function removeMatchResult(MatchResult $matchResult): self
    {
        // set the owning side to null (unless already changed)
        if ($this->matchResults->removeElement($matchResult) && $matchResult->getTourney() === $this) {
            $matchResult->setTourney(null);
        }

        return $this;
    }

}
