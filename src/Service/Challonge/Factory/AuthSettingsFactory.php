<?php

declare(strict_types=1);

namespace App\Service\Challonge\Factory;

use App\Service\Challonge\Dto\ChallongeAuthDto;

class AuthSettingsFactory
{
    public const REQUESTED_SCOPES = [
        'me',
        'tournaments:read',
        'tournaments:write',
        'matches:read',
        'matches:write',
        'participants:read',
        'participants:write'
    ];

    public function createSettings(): ChallongeAuthDto
    {
        return new ChallongeAuthDto(
            self::REQUESTED_SCOPES,
            $_ENV['CHALLONGE_CLIENT_ID'],
            $_ENV['CHALLONGE_REDIRECT_URI']
        );
    }
}