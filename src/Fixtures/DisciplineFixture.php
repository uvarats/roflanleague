<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\Discipline;
use App\Entity\Enum\DisciplineType;
use App\Fixtures\Base\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class DisciplineFixture extends BaseFixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $discipline = $this->getRandomDiscipline();

            $manager->persist($discipline);
        }

        $manager->flush();
    }

    private function getRandomDiscipline(): Discipline
    {
        $faker = $this->faker;

        $discipline = new Discipline();
        $name = ucfirst($faker->word);

        $discipline->setName($name)
            ->setDescription($faker->paragraph)
            ->setType($this->getRandomDisciplineType());

        return $discipline;
    }

    private function getRandomDisciplineType(): DisciplineType
    {
        $randomizer = $this->randomizer;
        $types = DisciplineType::cases();

        $key = $randomizer->pickArrayKeys($types, 1)[0];

        return $types[$key];
    }
}
