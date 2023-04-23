<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Discipline;
use App\Entity\User;
use App\Entity\UserRating;
use App\Factory\Rating\UserRatingFactoryFactory;
use App\Repository\UserRatingRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class RatingService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRatingRepository $ratingRepository,
        private UserRatingFactoryFactory $factoryFactory,
    ) {
    }

    public function getOrCreateRating(User $user, Discipline $discipline): UserRating
    {
        $rating = $this->getUserRating($user, $discipline);

        if ($rating === null) {
            $rating = $this->createRating($user, $discipline);
        }

        return $rating;
    }

    public function getUserRating(User $user, Discipline $discipline): ?UserRating
    {
        return $this->ratingRepository->getUserRatingInDiscipline($user, $discipline);
    }

    public function createRating(User $user, Discipline $discipline, bool $persist = true): UserRating
    {
        $ratingFactory = $this->factoryFactory->getFactory($user, $discipline);
        $rating = $ratingFactory->create();

        if ($persist) {
            $this->em->persist($rating);
        }

        return $rating;
    }

    public function isUserRatingInDisciplineExists(User $user, Discipline $discipline): bool
    {
        return $this->ratingRepository->hasUserRatingInDiscipline($user, $discipline);
    }
}