<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto;

final readonly class ChallongeTokenDto
{
    public function __construct(
        public string $access_token,
        public string $refresh_token,
        public string $token_type,
        public int    $expires_in,
        public string $scope,
        public int    $created_at
    )
    {
    }

    /**
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public static function fromJson(string $json): ChallongeTokenDto
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $class = new \ReflectionClass(self::class);

        return $class->newInstanceArgs($data);
    }
}