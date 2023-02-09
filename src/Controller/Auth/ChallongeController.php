<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Service\Challonge\ChallongeOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ChallongeController extends AbstractController
{
    public function __construct(
        private readonly ChallongeOAuthService $challongeOAuth
    )
    {
    }

    #[Route('/auth/challonge', name: 'app_auth_challonge')]
    public function index(
        #[CurrentUser] User $user
    ): Response
    {
        $challongeToken = $user->getChallongeToken();

        if ($challongeToken !== null) {
            $this->addFlash('error', 'У Вас уже подключен аккаунт Challonge!');
            return $this->redirectToRoute('app_profile');
        }

        $redirectUrl = $this->challongeOAuth->getAuthUrl();

        return $this->redirect($redirectUrl);
    }
}
