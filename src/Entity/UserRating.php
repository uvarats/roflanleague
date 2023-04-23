<?php

namespace App\Entity;

use App\Repository\UserRatingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRatingRepository::class)]
class UserRating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\ManyToOne(cascade: ['refresh', 'remove'], inversedBy: 'userRatings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $participant = null;

    #[ORM\ManyToOne(cascade: ['refresh', 'remove'], inversedBy: 'userRatings')]
    private ?Discipline $discipline = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'rating', targetEntity: UserRatingUpdate::class)]
    private Collection $ratingUpdates;

    public function __construct()
    {
        $this->ratingUpdates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): self
    {
        $this->participant = $participant;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, UserRatingUpdate>
     */
    public function getRatingUpdates(): Collection
    {
        return $this->ratingUpdates;
    }

    public function addRatingUpdate(UserRatingUpdate $ratingUpdate): self
    {
        if (!$this->ratingUpdates->contains($ratingUpdate)) {
            $this->ratingUpdates->add($ratingUpdate);
            $ratingUpdate->setRating($this);
        }

        return $this;
    }

    public function removeRatingUpdate(UserRatingUpdate $ratingUpdate): self
    {
        if ($this->ratingUpdates->removeElement($ratingUpdate)) {
            // set the owning side to null (unless already changed)
            if ($ratingUpdate->getRating() === $this) {
                $ratingUpdate->setRating(null);
            }
        }

        return $this;
    }
}
