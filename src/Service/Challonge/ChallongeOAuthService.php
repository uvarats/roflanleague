<?php

declare(strict_types=1);

namespace App\Service\Challonge;

use App\Dto\ChallongeCodeDTO;
use App\Entity\ChallongeToken;
use App\Entity\User;
use App\Service\Challonge\Dto\ChallongeAuthUrlDto;
use App\Service\Challonge\Dto\ChallongeTokenDto;
use App\Service\Challonge\Dto\TokenRequestDto;
use App\Service\Challonge\Factory\AuthSettingsFactory;
use App\Service\Interface\Arrayable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ChallongeOAuthService
{
    public const DOMAIN = 'challonge.com';

    private string $oauthUrl;
    private string $tokenUrl;

    public function __construct(
        private HttpClientInterface $client,
        private AuthSettingsFactory $settingsFactory,
        private EntityManagerInterface $entityManager,
    )
    {
        $domain = self::DOMAIN;
        $oauthUrl = "https://api.{$domain}/oauth";
        $this->oauthUrl = "{$oauthUrl}/authorize";
        $this->tokenUrl = "{$oauthUrl}/token";
    }

    public function getAuthUrl(): string
    {
        /**
         * https://api.challonge.com/oauth/authorize?scope=me tournaments:read tournaments:write matches:read matches:write participants:read&client_id=28fbbba5bdd0f6a44fe5a9332c32c442ffb25f866d31915c02c2605222c138e1&redirect_uri=https://localhost:8000/api/challonge/oauth&response_type=code
         */
        return $this->buildAuthUrl();
    }

    private function buildAuthUrl(): string
    {
        $authUrlDto = $this->settingsFactory->createUrlInfo();

        $queryParams = $this->buildQueryString($authUrlDto);

        return $this->oauthUrl . '?' . $queryParams;
    }

    private function buildQueryString(Arrayable $arrayableDto): string
    {
        $query = http_build_query($arrayableDto->toArray());

        return urldecode($query);
    }

    public function requestToken(ChallongeCodeDTO $challongeCodeDTO, User $user): ChallongeToken
    {
        $tokenDto = $this->makeTokenRequest($challongeCodeDTO);

        return $this->createModel($tokenDto, $user);
    }

    private function makeTokenRequest(ChallongeCodeDTO $challongeCode): ChallongeTokenDto
    {
        $tokenRequestData = TokenRequestDto::fromCode($challongeCode);

        $response = $this->client->request(
            'POST',
            $this->tokenUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $tokenRequestData->toArray(),
            ]
        );

        return ChallongeTokenDto::fromJson($response->getContent());
    }

    private function createModel(ChallongeTokenDto $tokenDto, User $user): ChallongeToken
    {
        $challongeToken = new ChallongeToken();

        $tokenExpiresAtTimestamp = $tokenDto->expires_in + $tokenDto->created_at;
        $expiresAt = new \DateTimeImmutable();
        $expiresAt->setTimestamp($tokenExpiresAtTimestamp);

        $challongeToken->setAccessToken($tokenDto->access_token)
            ->setRefreshToken($tokenDto->refresh_token)
            ->setRelatedUser($user)
            ->setAccessTokenExpiresAt($expiresAt);

        return $challongeToken;
    }

    public function refreshToken(ChallongeToken $challongeToken) {

    }
}