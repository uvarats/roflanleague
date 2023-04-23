<?php

declare(strict_types=1);

namespace App\Factory\Rating;

use App\Entity\Discipline;
use App\Entity\User;
use App\Entity\UserRating;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserRatingFactory
{
    public function __construct(
        private User $user,
        private Discipline $discipline,
    )
    {
    }

    public function create(): UserRating
    {
        $rating = new UserRating();
        $rating->setValue(10)
            ->setParticipant($this->user)
            ->setDiscipline($this->discipline)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        return $rating;
    }
}