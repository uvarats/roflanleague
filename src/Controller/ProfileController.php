<?php

namespace App\Controller;

use App\Entity\MatchResult;
use App\Entity\User;
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
        private readonly EntityManagerInterface $em,
        private readonly PaginatorInterface     $paginator,
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
        Request $request
    ): Response
    {
        if ($user->isBanned() && !$this->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException("This user is banned.");
        }

        $users = $this->em->getRepository(User::class);
        $position = $users->getTopPosition($user);

        $matches = $this->em->getRepository(MatchResult::class);
        $userMatchesQuery = $matches->getUserMatches($user);

        $resultsPagination = $this->paginator->paginate(
            $userMatchesQuery,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'results' => $resultsPagination,
            'lastMatches' => $matches->getLastMatches($user, 5),
            'position' => $position,
        ]);
    }
}
