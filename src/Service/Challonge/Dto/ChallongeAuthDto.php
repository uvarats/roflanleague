<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto;

use App\Service\Interface\UrlQueryConvertable;

final readonly class ChallongeAuthDto implements UrlQueryConvertable
{

    public function __construct(
        public array  $scope,
        public string $clientId,
        public string $redirectUri,
        public string $responseType = 'code'
    )
    {
    }

    public function toUrlQueryParams(): string
    {
        $data = get_object_vars($this);

        $scope = implode(' ', $this->scope);
        $data['scope'] = $scope;

        return http_build_query($data);
    }
}