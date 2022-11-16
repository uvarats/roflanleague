<?php

namespace App\Controller;

use App\Entity\Tourney;
use App\Entity\TourneyState;
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
        dd(TourneyState::STARTED->value);
        $tourneyRepository = $this->em->getRepository(Tourney::class);
        $tourneys = $tourneyRepository->findOneBy(['state' => 'started']);
        return $this->render('season/index.html.twig', [
            'tourneys' => $tourneys,
        ]);
    }
}
