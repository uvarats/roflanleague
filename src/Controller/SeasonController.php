<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MatchOdds;
use App\Entity\Tourney;
use App\Service\ChallongeService;
use App\Service\OddsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SeasonController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ChallongeService       $challonge,
        private readonly OddsService            $odds
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
    #[IsGranted(
        'IS_AUTHENTICATED_FULLY',
        message: 'Данную страницу могут просматривать только зарегистрированные пользователи'
    )]
    public function tourney(Tourney $tourney): Response
    {
        $matches = $this->challonge->getMatches($tourney);
        /** @var MatchOdds[] $odds */
        $odds = $this->odds->getMatchesOdds($matches, $tourney)->all();
        return $this->render('season/tourney.html.twig', [
            'tourney' => $tourney,
            'odds' => $odds,
        ]);
    }
}
