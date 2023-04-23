<?php

namespace App\Entity;

use App\Entity\Enum\TourneyState;
use App\Repository\TourneyRepository;
use App\Service\Report\Interface\ReportableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourneyRepository::class)]
class Tourney implements ReportableInterface
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

    #[ORM\Column]
    private bool $is_ranked = false;

    #[ORM\ManyToOne(inversedBy: 'tourneys')]
    private ?Discipline $discipline = null;

    #[ORM\OneToOne(mappedBy: 'tourney', cascade: ['persist', 'remove'])]
    private ?TourneyInvite $invite = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;


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

    public function isIsRanked(): ?bool
    {
        return $this->is_ranked;
    }

    public function setIsRanked(bool $is_ranked): self
    {
        $this->is_ranked = $is_ranked;

        return $this;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getInvite(): ?TourneyInvite
    {
        return $this->invite;
    }

    public function setInvite(TourneyInvite $invite): self
    {
        // set the owning side of the relation if necessary
        if ($invite->getTourney() !== $this) {
            $invite->setTourney($this);
        }

        $this->invite = $invite;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isEnded(): bool
    {
        $state = $this->getState();

        return $state === TourneyState::ENDED->value;
    }

    public function getReportName(): string
    {
        return 'tourney_' . $this->getId() . '_report.pdf';
    }
}
