<?php

declare(strict_types=1);

namespace App\Dto\Trait;

use App\Exception\ConstructorMissingException;
use ReflectionClass;

trait ReflectionDTO
{
    public static function from(array $data): static
    {
        $class = new ReflectionClass(static::class);

        if ($class->getConstructor() === null) {
            throw new ConstructorMissingException();
        }

        return $class->newInstanceArgs($data);
    }
}