<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ChallongeToken;
use App\Entity\User;
use App\Exception\Challonge\UserMissingTokenException;
use App\Service\Challonge\ChallongeOAuthService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final readonly class TokenService
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private ChallongeOAuthService $challongeOAuth,
    )
    {
    }

    /**
     * Returns current user's challonge token. If token missing - throws exception.
     * Checks if token fresh and refreshing if expired.
     *
     * @return ChallongeToken
     * @throws UserMissingTokenException
     */
    public function getChallongeToken(): ChallongeToken
    {
        /** @var User $user */
        $user = $this->getCurrentUser();
        $token = $user->getChallongeToken();

        if (!$token instanceof \App\Entity\ChallongeToken) {
            throw new UserMissingTokenException();
        }

        $this->challongeOAuth->refreshTokenIfExpired($token);

        return $token;
    }

    public function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        $user = $token?->getUser();

        if ($user !== null && !$user instanceof User) {
            throw new \TypeError();
        }

        return $user;
    }

}