<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Discipline;
use App\Repository\DisciplineRepository;
use App\Repository\TourneyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisciplineController extends AbstractController
{
    #[Route('/disciplines', name: 'app_disciplines')]
    public function disciplines(DisciplineRepository $disciplineRepository): Response
    {
        return $this->render('discipline/index.html.twig', [
            'disciplines' => $disciplineRepository->findAll(),
        ]);
    }

    #[Route('/discipline/{id}/tourneys', name: 'app_discipline_tourneys')]
    public function disciplineTourneys(
        Discipline $discipline,
        TourneyRepository $tourneyRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {

        $pagination = $paginator->paginate(
            $tourneyRepository->getByDisciplineQuery($discipline),
            (int)$request->query->get('page', 1),
            (int)$request->query->get('perPage', 30),
        );

        return $this->render('discipline/tourneys.html.twig', [
            'discipline' => $discipline,
            'tourneys' => $pagination,
        ]);
    }
}