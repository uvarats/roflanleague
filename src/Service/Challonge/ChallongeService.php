<?php

declare(strict_types=1);

namespace App\Service\Challonge;

use App\Entity\ChallongeToken;
use App\Entity\User;
use App\Exception\Challonge\UserMissingTokenException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final readonly class ChallongeService
{
    private ChallongeToken $challongeToken;

    public function __construct(
        TokenStorageInterface         $tokenStorage,
        private ChallongeOAuthService $challongeOAuth
    )
    {
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $token = $user->getChallongeToken();

        if ($token === null) {
            throw new UserMissingTokenException();
        }

        $token = $this->challongeOAuth->refreshToken($token);

        if ($token->isAccessTokenExpired()) {
            $this->challongeOAuth->refreshToken($token);
        }
    }
}