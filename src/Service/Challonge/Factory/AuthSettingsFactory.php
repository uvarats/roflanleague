<?php

declare(strict_types=1);

namespace App\Service\Challonge\Factory;

use App\Service\Challonge\Dto\ChallongeAuthUrlDto;

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

    public function createUrlInfo(): ChallongeAuthUrlDto
    {
        $implodedScopes = implode(' ', self::REQUESTED_SCOPES);

        return new ChallongeAuthUrlDto(
            scope: $implodedScopes,
            client_id: $_ENV['CHALLONGE_CLIENT_ID'],
            redirect_uri: $_ENV['CHALLONGE_REDIRECT_URI']
        );
    }
}