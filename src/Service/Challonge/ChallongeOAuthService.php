<?php

declare(strict_types=1);

namespace App\Service\Challonge;

use App\Dto\ChallongeCodeDTO;
use App\Entity\ChallongeToken;
use App\Entity\User;
use App\Service\Challonge\Dto\Auth\ChallongeTokenDto;
use App\Service\Challonge\Dto\Auth\RefreshTokenRequestDto;
use App\Service\Challonge\Dto\Auth\TokenRequestDto;
use App\Service\Challonge\Factory\AuthSettingsFactory;
use App\Service\Interface\Arrayable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class ChallongeOAuthService
{
    /**
     * @var string
     */
    public const DOMAIN = 'challonge.com';

    private string $oauthUrl;

    private string $tokenUrl;

    public function __construct(
        private HttpClientInterface    $client,
        private AuthSettingsFactory    $settingsFactory,
        private EntityManagerInterface $entityManager,
    )
    {
        $domain = self::DOMAIN;
        $oauthUrl = sprintf('https://api.%s/oauth', $domain);
        $this->oauthUrl = sprintf('%s/authorize', $oauthUrl);

        $this->tokenUrl = sprintf('%s/token', $oauthUrl);
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

        $response = $this->request($this->tokenUrl, $tokenRequestData);

        return ChallongeTokenDto::fromJson($response->getContent());
    }

    public function refreshTokenIfExpired(ChallongeToken $challongeToken): void
    {
        if ($challongeToken->isAccessTokenExpired()) {
            $this->refreshToken($challongeToken);
        }
    }

    public function refreshToken(ChallongeToken $challongeToken): ChallongeToken
    {
        $refreshToken = $challongeToken->getRefreshToken();
        $tokenDto = $this->refreshTokenRequest($refreshToken);

        $token = $this->updateModelFromDto($challongeToken, $tokenDto);

        $this->entityManager->flush();

        return $token;
    }

    private function refreshTokenRequest(string $refreshToken): ChallongeTokenDto
    {
        $refreshTokenRequest = RefreshTokenRequestDto::from($refreshToken);

        $response = $this->request($this->tokenUrl, $refreshTokenRequest);

        return ChallongeTokenDto::fromJson($response->getContent());
    }

    private function createModel(ChallongeTokenDto $tokenDto, User $user): ChallongeToken
    {
        $challongeToken = new ChallongeToken();
        $challongeToken->setRelatedUser($user);

        return $this->updateModelFromDto($challongeToken, $tokenDto);
    }

    private function updateModelFromDto(
        ChallongeToken    $challongeToken,
        ChallongeTokenDto $tokenDto
    ): ChallongeToken
    {
        $tokenExpiresAtTimestamp = $tokenDto->expires_in + $tokenDto->created_at;
        $expiresAt = new \DateTimeImmutable();
        $expiresAt = $expiresAt->setTimestamp($tokenExpiresAtTimestamp);

        return $challongeToken->setAccessToken($tokenDto->access_token)
            ->setRefreshToken($tokenDto->refresh_token)
            ->setAccessTokenExpiresAt($expiresAt);
    }

    private function request(string $url, Arrayable $data, string $method = 'POST'): ResponseInterface
    {
        return $this->client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $data->toArray(),
            ]
        );
    }
}