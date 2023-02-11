<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/rating/{page}', name: 'app_rating')]
    public function rating(int $page = 1): Response
    {
        $users = $this->em->getRepository(User::class);
        $usersPerPage = 50;
        $count = $users->getCountVerifiedAndNotBanned();
        /** @var User[] $top */
        $top = $users->getRatingTop($page, $usersPerPage);

        $pagesCount = min(ceil($count / $usersPerPage), 10);
        return $this->render('main/rating.html.twig', [
            'users' => $top,
            'currentPage' => $page,
            'usersPerPage' => $usersPerPage,
            'pagesCount' => $pagesCount,
        ]);
    }
}
