<?php

declare(strict_types=1);

namespace App\Dto\Base;

use Brick\JsonMapper\JsonMapper;
use Brick\JsonMapper\NameMapper\CamelCaseToSnakeCaseMapper;
use Brick\JsonMapper\NameMapper\NullMapper;
use Brick\JsonMapper\NameMapper\SnakeCaseToCamelCaseMapper;
use Brick\JsonMapper\OnExtraProperties;
use Brick\JsonMapper\OnMissingProperties;

class MapperFactory
{
    public function getMapper(bool $snakeToCamel = false): JsonMapper
    {
        $jsonToPhpNameMapper = new NullMapper();
        $phpToJsonNameMapper = new NullMapper();
        if ($snakeToCamel) {
            $jsonToPhpNameMapper = new SnakeCaseToCamelCaseMapper();
            $phpToJsonNameMapper = new CamelCaseToSnakeCaseMapper();
        }

        return new JsonMapper(
            onExtraProperties: OnExtraProperties::IGNORE,
            onMissingProperties: OnMissingProperties::SET_NULL,
            jsonToPhpNameMapper: $jsonToPhpNameMapper,
            phpToJsonNameMapper: $phpToJsonNameMapper,
        );
    }

}