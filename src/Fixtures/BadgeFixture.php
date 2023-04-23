<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\Badge;
use App\Fixtures\Base\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class BadgeFixture extends BaseFixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $badge = $this->getRandomBadge();

            $manager->persist($badge);
        }

        $manager->flush();
    }

    private function getRandomBadge(): Badge
    {
        $faker = $this->faker;

        $badge = new Badge();
        $priority = $this->randomizer->getInt(10, 200);

        $badge->setName($faker->realText(25))
            ->setText($faker->text(150))
            ->setHexCode($faker->hexColor)
            ->setPriority($priority);

        return $badge;
    }
}