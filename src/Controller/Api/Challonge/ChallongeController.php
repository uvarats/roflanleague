<?php

namespace App\Controller\Api\Challonge;

use App\Dto\ChallongeCodeDTO;
use App\Entity\User;
use App\Service\Challonge\ChallongeOAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ChallongeController extends AbstractController
{


    public function __construct(
        private readonly ChallongeOAuthService $oauthService,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/api/challonge/oauth', name: 'app_challonge_oauth')]
    public function index(
        #[CurrentUser] User $user,
        Request $request
    ): RedirectResponse
    {
        $code = $request->query->get('code');

        $codeDto = ChallongeCodeDTO::fromCode($code);
        $challongeToken = $this->oauthService->requestToken($codeDto, $user);

        $this->entityManager->persist($challongeToken);
        $this->entityManager->flush();

        $this->addFlash('success', 'Аккаунт Challonge успешно привязан!');
        return $this->redirectToRoute('app_profile');
    }
}
