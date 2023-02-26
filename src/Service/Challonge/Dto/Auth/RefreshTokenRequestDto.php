<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto\Auth;

use App\Service\Interface\Arrayable;
use App\Service\Trait\ArrayableTrait;

final readonly class RefreshTokenRequestDto implements Arrayable
{
    use ArrayableTrait;
    public function __construct(
        public string $refresh_token,
        public string $client_id,
        public string $redirect_uri,
        public string $grant_type = 'refresh_token',
    )
    {
    }

    public static function from(string $refreshToken): RefreshTokenRequestDto
    {
        return new self(
            refresh_token: $refreshToken,
            client_id: $_ENV['CHALLONGE_CLIENT_ID'],
            redirect_uri: $_ENV['CHALLONGE_REDIRECT_URI'],
        );
    }
}