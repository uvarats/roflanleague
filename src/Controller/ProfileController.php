<?php

namespace App\Controller;

use App\Entity\MatchResult;
use App\Entity\User;
use App\Repository\MatchResultRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly PaginatorInterface     $paginator,
        private readonly UserRepository $users,
        private readonly MatchResultRepository $matches,
    )
    {
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(#[CurrentUser] User $user, Request $request): Response
    {
        return $this->concreteProfile($user, $request);
    }


    #[Route('/u/{id}', name: 'app_concrete_profile')]
    public function concreteProfile(
        #[MapEntity(expr: 'repository.getFullUser(id)')] User $user,
        Request $request,
    ): Response
    {
        $this->denyAccessUnlessGranted('view', $user);

        $position = $this->users->getTopPosition($user);

        $userMatchesQuery = $this->matches->getUserMatches($user);

        $resultsPagination = $this->paginator->paginate(
            $userMatchesQuery,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'results' => $resultsPagination,
            'lastMatches' => $this->matches->getLastMatches($user, 5),
            'position' => $position,
        ]);
    }
}
