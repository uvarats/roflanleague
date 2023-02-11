<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['username'], message: 'Аккаунт с таким ником уже существует')]
#[UniqueEntity(fields: ['email'], message: 'Аккаунт с такой почтой уже существует')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 24, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\ManyToMany(targetEntity: Badge::class, mappedBy: 'users')]
    #[ORM\OrderBy(['priority' => 'DESC'])]
    private Collection $badges;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isBanned = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => '2022-03-13 18:22:35'])]
    private ?\DateTimeInterface $registerDate = null;

    #[ORM\Column(options: ['default' => 100])]
    private ?int $rating = null;

    #[ORM\ManyToMany(targetEntity: Tourney::class, mappedBy: 'participants')]
    private Collection $tourneys;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: UserRating::class, orphanRemoval: true)]
    private Collection $userRatings;

    #[ORM\OneToOne(mappedBy: 'relatedUser', cascade: ['persist', 'remove'])]
    private ?ChallongeToken $challongeToken = null;


    public function __construct()
    {
        $this->badges = new ArrayCollection();
        $this->tourneys = new ArrayCollection();
        $this->userRatings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Badge>
     */
    public function getBadges(): Collection
    {
        return $this->badges;
    }

    public function addBadge(Badge $badge): self
    {
        if (!$this->badges->contains($badge)) {
            $this->badges[] = $badge;
            $badge->addUser($this);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): self
    {
        if ($this->badges->removeElement($badge)) {
            $badge->removeUser($this);
        }

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getRegisterDate(): ?\DateTimeInterface
    {
        return $this->registerDate;
    }

    public function setRegisterDate(\DateTimeInterface $registerDate): self
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

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
            $this->tourneys[] = $tourney;
            $tourney->addParticipant($this);
        }

        return $this;
    }

    public function removeTourney(Tourney $tourney): self
    {
        if ($this->tourneys->removeElement($tourney)) {
            $tourney->removeParticipant($this);
        }

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
            $userRating->setParticipant($this);
        }

        return $this;
    }

    public function removeUserRating(UserRating $userRating): self
    {
        if ($this->userRatings->removeElement($userRating)) {
            // set the owning side to null (unless already changed)
            if ($userRating->getParticipant() === $this) {
                $userRating->setParticipant(null);
            }
        }

        return $this;
    }

    public function getChallongeToken(): ?ChallongeToken
    {
        return $this->challongeToken;
    }

    public function setChallongeToken(ChallongeToken $challongeToken): self
    {
        // set the owning side of the relation if necessary
        if ($challongeToken->getRelatedUser() !== $this) {
            $challongeToken->setRelatedUser($this);
        }

        $this->challongeToken = $challongeToken;

        return $this;
    }

    public function isChallongeConnected(): bool
    {
        return $this->getChallongeToken() === null;
    }
}
