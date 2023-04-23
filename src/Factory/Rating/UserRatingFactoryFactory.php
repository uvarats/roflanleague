<?php

declare(strict_types=1);

namespace App\Factory\Rating;

use App\Entity\Discipline;
use App\Entity\User;

final readonly class UserRatingFactoryFactory
{
    public function getFactory(User $user, Discipline $discipline): UserRatingFactory
    {
        return new UserRatingFactory($user, $discipline);
    }
}