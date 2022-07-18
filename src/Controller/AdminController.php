<?php

namespace App\Controller;

use App\Entity\Tourney;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');



        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/tourney/{id}', name: 'app_tourney_settings')]
    public function tourneySettings(Tourney $tourney) {
        return $this->render('admin/tourney_settings.html.twig', []);
    }

    #[Route('/admin/tourneys', name: 'app_tourneys')]
    public function tourneys(): Response
    {

        return $this->render('admin/tourneys.html.twig', []);
    }
}
