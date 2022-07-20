<?php

namespace App\Controller;

use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users/{page}', name: 'app_admin_users')]
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

    #[Route('/admin/users/manage/{id}', name: 'app_admin_manage_user')]
    public function manage(User $user): Response
    {
        return $this->render('admin/manage_user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/tourney/{id}', name: 'app_tourney_settings')]
    public function tourneySettings(Tourney $tourney): Response
    {
        return $this->render('admin/tourney_settings.html.twig', []);
    }

    #[Route('/admin/tourneys', name: 'app_tourneys')]
    public function tourneys(): Response
    {

        return $this->render('admin/tourneys.html.twig', []);
    }
}
