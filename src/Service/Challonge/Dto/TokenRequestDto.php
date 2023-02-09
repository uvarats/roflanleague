<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto;

use App\Dto\ChallongeCodeDTO;
use App\Service\Interface\Arrayable;
use App\Service\Trait\ArrayableTrait;

final readonly class TokenRequestDto implements Arrayable
{
    use ArrayableTrait;
    public function __construct(
        public string $client_id,
        public string $client_secret,
        public string $code,
        public string $redirect_uri,
        public string $grant_type = 'authorization_code',
    )
    {
    }

    public static function fromCode(ChallongeCodeDTO $codeDTO): TokenRequestDto
    {
        return new self(
            client_id: $_ENV['CHALLONGE_CLIENT_ID'],
            client_secret: $_ENV['CHALLONGE_CLIENT_SECRET'],
            code: $codeDTO->code,
            redirect_uri: $_ENV['CHALLONGE_REDIRECT_URI'],
        );
    }
}