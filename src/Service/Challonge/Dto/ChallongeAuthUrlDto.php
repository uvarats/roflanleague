<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto;

use App\Service\Interface\Arrayable;

final readonly class ChallongeAuthUrlDto implements Arrayable
{

    public function __construct(
        public string $scope,
        public string $client_id,
        public string $redirect_uri,
        public string $response_type = 'code'
    )
    {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}