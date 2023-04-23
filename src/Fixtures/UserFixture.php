<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Random\Randomizer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private readonly Generator $faker;
    private readonly Randomizer $randomizer;

    public function __construct(private readonly UserPasswordHasherInterface $hasher) {
        $this->faker = Factory::create();
        $this->randomizer = new Randomizer();
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 100; $i++) {
            $user = $this->getRandomUser();

            $manager->persist($user);
        }

        $admin = $this->getAdmin();
        $manager->persist($admin);

        $manager->flush();
    }

    private function getRandomUser(): User
    {
        $faker = $this->faker;

        $user = new User();
        $rating = $this->randomizer->getInt(10, 300);

        $user->setUsername($faker->userName)
            ->setIsVerified(true)
            ->setIsBanned($faker->boolean(10))
            ->setEmail($faker->email)
            ->setRating($rating)
            ->setRegisterDate($faker->dateTimeThisMonth);

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        return $user;
    }

    private function getAdmin(): User
    {
        $user = new User();

        $user->setUsername('admin')
            ->setRegisterDate(new \DateTimeImmutable())
            ->setEmail('admin@adm.in')
            ->setIsVerified(true)
            ->setIsBanned(false)
            ->setRating(10)
            ->setRoles(['ROLE_ADMIN']);

        $password = $this->hasher->hashPassword($user, '12345admin');
        $user->setPassword($password);

        return $user;
    }

    public function getDependencies(): array
    {
        return [
            BadgeFixture::class
        ];
    }
}
