<?php

declare(strict_types=1);

namespace App\Service\Challonge;

use App\Service\Challonge\Factory\AuthSettingsFactory;
use Psr\Http\Client\ClientInterface;

readonly class ChallongeOAuthService
{
    public const DOMAIN = 'challonge.com';

    private string $oauthUrl;

    public function __construct(
        private ClientInterface     $client,
        private AuthSettingsFactory $settingsFactory
    )
    {
        $domain = self::DOMAIN;
        $this->oauthUrl = "https://api.{$domain}/oauth/authorize";
    }

    public function getAuthUrl(): string
    {
        return $this->buildAuthUrl();
    }

    private function buildAuthUrl(): string
    {
        $settings = $this->settingsFactory->createSettings();

        $queryParams = $settings->toUrlQueryParams();

        // TODO: make this shit work
        return $this->oauthUrl . '?' . $queryParams;
    }
}