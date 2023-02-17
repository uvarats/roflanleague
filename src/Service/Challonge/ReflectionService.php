<?php

declare(strict_types=1);

namespace App\Service\Challonge;

class ReflectionService
{
    public static function make(): ReflectionService
    {
        return new self();
    }

    public function findParameter(\ReflectionMethod $method, string $parameterName): ?\ReflectionParameter
    {
        $parameters = $method->getParameters();
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === $parameterName) {
                return $parameter;
            }
        }

        return null;
    }
}