<?php

namespace App\Controller;

use App\Entity\MatchResult;
use App\Service\ChallongeService;
use App\Service\MatchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{
    public function __construct(
        private MatchService $matchService,
        private ChallongeService $challonge,
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/match/result', name: 'app_match_result')]
    public function setResult(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $result = $this->matchService->loadResults($data);



        return $this->json(1);
    }

    #[Route('/match/random', name: 'app_match_random')]
    public function randomResult() {

    }
}