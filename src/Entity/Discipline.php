<?php

namespace App\Entity;

use App\Repository\DisciplineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisciplineRepository::class)]
class Discipline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'discipline', targetEntity: UserRating::class)]
    private Collection $userRatings;

    #[ORM\OneToMany(mappedBy: 'discipline', targetEntity: Tourney::class)]
    private Collection $tourneys;

    public function __construct()
    {
        $this->userRatings = new ArrayCollection();
        $this->tourneys = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, UserRating>
     */
    public function getUserRatings(): Collection
    {
        return $this->userRatings;
    }

    public function addUserRating(UserRating $userRating): self
    {
        if (!$this->userRatings->contains($userRating)) {
            $this->userRatings->add($userRating);
            $userRating->setDiscipline($this);
        }

        return $this;
    }

    public function removeUserRating(UserRating $userRating): self
    {
        // set the owning side to null (unless already changed)
        if ($this->userRatings->removeElement($userRating) && $userRating->getDiscipline() === $this) {
            $userRating->setDiscipline(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tourney>
     */
    public function getTourneys(): Collection
    {
        return $this->tourneys;
    }

    public function addTourney(Tourney $tourney): self
    {
        if (!$this->tourneys->contains($tourney)) {
            $this->tourneys->add($tourney);
            $tourney->setDiscipline($this);
        }

        return $this;
    }

    public function removeTourney(Tourney $tourney): self
    {
        // set the owning side to null (unless already changed)
        if ($this->tourneys->removeElement($tourney) && $tourney->getDiscipline() === $this) {
            $tourney->setDiscipline(null);
        }

        return $this;
    }
}
