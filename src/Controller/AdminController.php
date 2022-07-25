<?php

namespace App\Controller;

use App\Entity\Tourney;
use App\Entity\User;
use App\Form\TourneyType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Framework\RequestConfig;

#[Route('/admin')]
class AdminController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
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

}
