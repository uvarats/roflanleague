<?php

declare(strict_types=1);

namespace App\Dto\Base;

class Data
{
    public static function fromJson(string $json, bool $snakeToCamel = false): static
    {
        $factory = new MapperFactory();
        $mapper = $factory->getMapper($snakeToCamel);

        return $mapper->map($json, static::class);
    }
}