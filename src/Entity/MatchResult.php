<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\Result;
use App\Repository\MatchResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchResultRepository::class)]
class MatchResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    private ?User $homePlayer = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    private ?User $awayPlayer = null;

    #[ORM\Column(type: 'string', length: 15, enumType: Result::class)]
    private ?Result $result = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\Column]
    private ?int $challongeMatchId = null;

    #[ORM\Column]
    private ?float $homePlayerOdds = null;

    #[ORM\Column]
    private ?float $awayPlayerOdds = null;

    #[ORM\ManyToOne(inversedBy: 'matchResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tourney $tourney = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomePlayer(): ?User
    {
        return $this->homePlayer;
    }

    public function setHomePlayer(?User $homePlayer): self
    {
        $this->homePlayer = $homePlayer;

        return $this;
    }

    public function getAwayPlayer(): ?User
    {
        return $this->awayPlayer;
    }

    public function setAwayPlayer(?User $awayPlayer): self
    {
        $this->awayPlayer = $awayPlayer;

        return $this;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function setResult(Result $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getHomePlayerRatingChange(): ?int
    {
        return $this->homePlayerRatingChange;
    }

    public function setHomePlayerRatingChange(int $homePlayerRatingChange): self
    {
        $this->homePlayerRatingChange = $homePlayerRatingChange;

        return $this;
    }

    public function getAwayPlayerRatingChange(): ?int
    {
        return $this->awayPlayerRatingChange;
    }

    public function setAwayPlayerRatingChange(int $awayPlayerRatingChange): self
    {
        $this->awayPlayerRatingChange = $awayPlayerRatingChange;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getChallongeMatchId(): ?int
    {
        return $this->challongeMatchId;
    }

    public function setChallongeMatchId(int $challongeMatchId): self
    {
        $this->challongeMatchId = $challongeMatchId;

        return $this;
    }

    public function getHomePlayerOdds(): ?float
    {
        return $this->homePlayerOdds;
    }

    public function setHomePlayerOdds(float $homePlayerOdds): self
    {
        $this->homePlayerOdds = $homePlayerOdds;

        return $this;
    }

    public function getAwayPlayerOdds(): ?float
    {
        return $this->awayPlayerOdds;
    }

    public function setAwayPlayerOdds(float $awayPlayerOdds): self
    {
        $this->awayPlayerOdds = $awayPlayerOdds;

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

    public function getWinner(): ?User
    {
        return match ($this->result) {
            Result::FIRST_WIN => $this->homePlayer,
            Result::SECOND_WIN => $this->awayPlayer,
            Result::TIE, Result::CANCELED, null => null,
        };
    }

    public function getLoser(): ?User
    {
        return match ($this->result) {
            Result::FIRST_WIN => $this->awayPlayer,
            Result::SECOND_WIN => $this->homePlayer,
            Result::TIE, Result::CANCELED, null => null,
        };
    }

    public function isWinner(User $user): bool
    {
        return $this->getWinner() === $user;
    }

    public function isLoser(User $user): bool
    {
        return !$this->isWinner($user);
    }
}
