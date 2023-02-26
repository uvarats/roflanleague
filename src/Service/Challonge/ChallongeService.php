<?php

declare(strict_types=1);

namespace App\Service\Challonge;

use App\Entity\ChallongeToken;
use App\Service\Challonge\Collection\Headers;
use App\Service\Challonge\Dto\Tournament\Tournament;
use App\Service\Challonge\Dto\User\User;
use App\Service\TokenService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ChallongeService
{
    /**
     * @var string
     */
    public const DOMAIN = 'challonge.com';

    private string $apiUri;

    private ChallongeToken $challongeToken;

    public function __construct(
        private TokenService        $tokenService,
        private HttpClientInterface $client,
    )
    {
        $this->challongeToken = $tokenService->getChallongeToken();

        $domain = self::DOMAIN;
        $this->apiUri = sprintf('https://api.%s/v2', $domain);
    }

    public function getUser(): User
    {
        $data = $this->request('me.json', 'GET');

        return User::from($data);
    }

    public function getTournaments(): array
    {
        $data = $this->request('tournaments.json', 'GET');

        return Tournament::populateCollection($data['data']);
    }

    public function getTournament(string $id): Tournament
    {
        $data = $this->request("tournaments/{$id}.json", 'GET');

        return Tournament::from($data);
    }

    public function createTournament(array $params) {
        $data = $this->request('tournaments.json', 'POST');
    }

    private function request(string $apiRelativeUri, string $method, array $body = [], array $headers = null): array
    {
        if ($headers === null) {
            $defaultHeaders = $this->getDefaultHeaders();
            $headers = $defaultHeaders->toArray();
        }

        $uri = $this->getUri($apiRelativeUri);

        $response = $this->client->request(
            $method,
            $uri,
            [
                'headers' => $headers,
                //'body' => $body,
            ]
        );

        return $response->toArray();
    }

    private function getUri(string $path): string
    {
        if ($path[0] === '/') {
            return $this->apiUri . $path;
        }

        return $this->apiUri . '/' . $path;
    }

    private function getDefaultHeaders(): Headers
    {
        $accessToken = $this->challongeToken->getAccessToken();

        return new Headers([
            'Authorization-Type' => 'v2',
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/vnd.api+json',
            'Accept' => 'application/json'
        ]);
    }
}