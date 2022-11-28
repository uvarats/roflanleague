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

    #[ORM\ManyToOne(inversedBy: 'matchResults')]
    private ?User $player1 = null;

    #[ORM\ManyToOne(inversedBy: 'matchResults')]
    private ?User $player2 = null;

    #[ORM\Column(type: 'string', length: 15, enumType: Result::class)]
    private ?Result $result = null;

    #[ORM\Column]
    private ?int $player1RatingChange = null;

    #[ORM\Column]
    private ?int $player2RatingChange = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\Column]
    private ?int $challongeMatchId = null;

    #[ORM\Column]
    private ?float $player1Odds = null;

    #[ORM\Column]
    private ?float $player2Odds = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer1(): ?User
    {
        return $this->player1;
    }

    public function setPlayer1(?User $player1): self
    {
        $this->player1 = $player1;

        return $this;
    }

    public function getPlayer2(): ?User
    {
        return $this->player2;
    }

    public function setPlayer2(?User $player2): self
    {
        $this->player2 = $player2;

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

    public function getPlayer1RatingChange(): ?int
    {
        return $this->player1RatingChange;
    }

    public function setPlayer1RatingChange(int $player1RatingChange): self
    {
        $this->player1RatingChange = $player1RatingChange;

        return $this;
    }

    public function getPlayer2RatingChange(): ?int
    {
        return $this->player2RatingChange;
    }

    public function setPlayer2RatingChange(int $player2RatingChange): self
    {
        $this->player2RatingChange = $player2RatingChange;

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

    public function getPlayer1Odds(): ?float
    {
        return $this->player1Odds;
    }

    public function setPlayer1Odds(float $player1Odds): self
    {
        $this->player1Odds = $player1Odds;

        return $this;
    }

    public function getPlayer2Odds(): ?float
    {
        return $this->player2Odds;
    }

    public function setPlayer2Odds(float $player2Odds): self
    {
        $this->player2Odds = $player2Odds;

        return $this;
    }
}
