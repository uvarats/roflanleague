<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Enum\TourneyState;
use App\Entity\MatchOdds;
use App\Entity\Tourney;
use App\Service\ChallongeService;
use App\Service\OddsService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TourneyController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ChallongeService       $challonge,
        private readonly OddsService            $odds,
        private readonly PaginatorInterface     $paginator
    )
    {
    }

    #[Route('/tourneys/{page}', name: 'app_tourneys')]
    public function index(int $page = 1): Response
    {
        $tourneyRepository = $this->em->getRepository(Tourney::class);
        $tourneys = $tourneyRepository->getByStateQuery(TourneyState::STARTED)->getResult();

        return $this->render('tourney/index.html.twig', [
            'tourneys' => $tourneys,
        ]);
    }

    #[Route('/history/{page}', name: 'app_history')]
    public function history(int $page = 1): Response
    {
        $tourneys = $this->em->getRepository(Tourney::class);
        $query = $tourneys->getByStateQuery(TourneyState::ENDED);

        $paginator = $this->paginator->paginate(
            $query,
            $page,
            10
        );

        return $this->render('tourney/history.html.twig', [
            'tourneys' => $paginator,
        ]);
    }

    #[Route('/tourney/{id}', name: 'app_tourney')]
    #[IsGranted(
        'IS_AUTHENTICATED_FULLY',
        message: 'Данную страницу могут просматривать только зарегистрированные пользователи'
    )]
    public function tourney(Tourney $tourney): Response
    {
        $matches = $this->challonge->getMatches($tourney);
        /** @var MatchOdds[] $odds */
        $odds = $this->odds->getMatchesOdds($matches, $tourney)->all();
        return $this->render('tourney/tourney.html.twig', [
            'tourney' => $tourney,
            'odds' => $odds,
        ]);
    }
}
