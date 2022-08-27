<?php

namespace App\Controller;

use App\Entity\Tourney;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeasonController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/season', name: 'app_season')]
    public function index(): Response
    {
        $tourneyRepository = $this->em->getRepository(Tourney::class);
        $tourneys = $tourneyRepository->getWithActiveSeason();
        return $this->render('season/index.html.twig', [
            'tourneys' => $tourneys,
        ]);
    }
}
