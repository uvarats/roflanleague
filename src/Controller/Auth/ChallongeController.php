<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\ChallongeTokenRepository;
use App\Service\Challonge\ChallongeOAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ChallongeController extends AbstractController
{
    public function __construct(
        private readonly ChallongeOAuthService $challongeOAuth,
        private readonly EntityManagerInterface $entityManager,
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
            return $this->redirectToRoute('app_settings');
        }

        $redirectUrl = $this->challongeOAuth->getAuthUrl();

        return $this->redirect($redirectUrl);
    }

    #[Route('/auth/challonge/disconnect', name: 'app_auth_challonge_disconnect')]
    public function disconnect(
        #[CurrentUser] User $user,
        ChallongeTokenRepository $tokenRepository
    ): RedirectResponse
    {
        // TODO: база стирается к чертовой матери, пофиксить (отсоединять токен от юзера)
        $token = $user->getChallongeToken();

        if ($token !== null) {
            $tokenRepository->remove($token, true);
        }

        return $this->redirectToRoute('app_settings');
    }
}
