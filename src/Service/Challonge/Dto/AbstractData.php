<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto;

use App\Exception\ConstructorMissingException;
use App\Service\Challonge\ReflectionService;

class AbstractData
{
    public static function from(array $data): static
    {
        if (isset($data['data'])) {
            $data = $data['data'];
        }

        $reflectionClass = new \ReflectionClass(static::class);

        $constructor = $reflectionClass->getConstructor();

        if (!$constructor instanceof \ReflectionMethod) {
            throw new ConstructorMissingException();
        }

        $constructorParams = [];
        $params = $constructor->getParameters();

        foreach ($params as $param) {
            $paramName = $param->getName();

            if (!isset($data[$paramName])) {
                continue;
            }

            $value = $data[$paramName];

            $type = $param->getType();

            if (!$type instanceof \ReflectionNamedType) {
                throw new \Exception('Supported only named types!');
            }

            if ($type->isBuiltin()) {
                $constructorParams[$paramName] = $value;
                continue;
            }

            $typeName = $type->getName();

            if (is_subclass_of($typeName, \BackedEnum::class)) {
                $constructorParams[$paramName] = $typeName::tryFrom($value);
                continue;
            }

            if (is_array($value) && is_subclass_of($typeName, AbstractData::class)) {
                $constructorParams[$paramName] = $typeName::from($value);
            }

        }

        return $reflectionClass->newInstanceArgs($constructorParams);
    }

    /**
     * @param array $data
     * @return static[]
     * @throws ConstructorMissingException
     */
    public static function populateCollection(array $data): array
    {
        $dataObjects = [];

        foreach ($data as $tournamentData) {
            $dataObjects[] = static::from($tournamentData);
        }

        return $dataObjects;
    }
}