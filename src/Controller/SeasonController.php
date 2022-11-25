<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tourney;
use App\Service\ChallongeService;
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
        $tourneys = $tourneyRepository->findBy(['state' => 'started']);

        return $this->render('season/index.html.twig', [
            'tourneys' => $tourneys,
        ]);
    }

    #[Route('/season/{id}', name: 'app_season_tourney')]
    public function tourney(Tourney $tourney, ChallongeService $service): Response
    {
        $matches = $service->getChallonge()->getMatches($tourney->getChallongeUrl());
        return $this->render('season/tourney.html.twig', [
            'tourney' => $tourney,
        ]);
    }
}
