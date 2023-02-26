<?php

namespace App\Controller\Admin;

use App\Entity\Badge;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
    }

    #[Route('', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/users/{page}', name: 'app_admin_users')]
    public function users(int $page = 1): Response
    {
        $users = $this->em->getRepository(User::class);
        $usersPerPage = 30;
        $count = $users->getCount();
        return $this->render('admin/users.html.twig', [
            'currentPage' => $page,
            'pagesCount' => ceil($count / $usersPerPage),
            'users' => $users->getListByPage($page, $usersPerPage),
        ]);
    }

    #[Route('/users/manage/{id}', name: 'app_admin_manage_user')]
    public function manage(User $user): Response
    {
        return $this->render('admin/manage_user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/switch', name: 'app_admin_user_switch', methods: ['POST'])]
    public function switchUser(int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if ($user !== null) {
            $userStatus = $user->isBanned();
            $user->setIsBanned(!$userStatus);
            $this->em->flush();
            return $this->json([
                'newStatus' => !$userStatus,
            ]);
        }

        return $this->json([
            'error' => 'User not found',
        ]);
    }

    #[Route('/user/{id}/verify', name: 'app_admin_user_verify', methods: ["POST"])]
    public function verify(int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if ($user !== null) {
            $user->setIsVerified(true);
            $this->em->flush();
            return $this->json([
                'success' => true,
            ]);
        }

        return $this->json([
            'error' => 'User not found',
        ]);
    }

    #[Route('/user/{id}/add-badge', name: 'app_admin_user_badge_add', methods: ["POST"])]
    public function addBadge(User $user, Request $request): JsonResponse
    {
        $badgeId = $request->request->get('id');
        $badge = $this->em->getRepository(Badge::class)->find($badgeId);
        if ($badge !== null) {
            if ($user->getBadges()->contains($badge)) {
                return $this->json([
                    'error' => 'User already has this badge',
                ]);
            }

            $user->addBadge($badge);
            $this->em->flush();
            return $this->json([
                'badge' => [
                    'id' => $badge->getId(),
                    'name' => $badge->getName(),
                    'hexCode' => $badge->getHexCode(),
                    'text' => $badge->getText(),
                ],
            ]);
        }

        return $this->json([
            'error' => 'Badge was not found',
        ]);
    }

    #[Route('/user/{id}/remove-badge', name: 'app_admin_user_badge_remove')]
    public function removeBadge(User $user, Request $request): JsonResponse
    {
        $badgeId = $request->request->get('id');
        $badge = $this->em->getRepository(Badge::class)->find($badgeId);
        if ($badge !== null) {
            $user->removeBadge($badge);
            $this->em->flush();
            return $this->json([
                'success' => true,
            ]);
        }

        return $this->json([
            'error' => 'Badge was not found',
        ]);
    }
}
