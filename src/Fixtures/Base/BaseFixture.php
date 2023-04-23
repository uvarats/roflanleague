<?php

declare(strict_types=1);

namespace App\Fixtures\Base;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Faker\Generator;
use Random\Randomizer;

abstract class BaseFixture extends Fixture
{
    protected readonly Generator $faker;
    protected readonly Randomizer $randomizer;

    public function __construct() {
        $this->faker = Factory::create();
        $this->randomizer = new Randomizer();
    }
}
