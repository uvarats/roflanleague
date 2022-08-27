<?php

namespace App\Controller;

use App\Entity\Tourney;
use App\Service\ChallongeService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class SeasonsAdminController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
    ) {

    }

    #[Route('/tourney/{id}/seasons', name: 'app_tourney_seasons')]
    public function tourneySeasons(Tourney $tourney): Response
    {
        return $this->render('admin/tourney/tourney_seasons.html.twig', [
            'tourney' => $tourney,
        ]);
    }

    #[Route('/tourney/{id}/season/new', name: 'app_tourney_season_new', methods: ['POST'])]
    public function newSeason(Tourney $tourney, ChallongeService $challonge) {
        if (!$tourney->getCurrentSeason()) {
            return $this->json([
                'error' => 'Please finish current season before starting new',
            ]);
        }
        $name = $tourney->getName() . " " . ($tourney->getSeasons()->count() + 1);
        $challongeTourney = $challonge->createTournament($name, "double elimination");
    }
}