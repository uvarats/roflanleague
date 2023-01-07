<?php

namespace App\Controller;

use App\Dto\MatchResultDTO;
use App\Service\MatchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MatchController extends AbstractController
{
    public function __construct(
        private MatchService $matchService,
    )
    {
    }

    #[Route('/match/result', name: 'app_match_result')]
    #[IsGranted('ROLE_ADMIN')]
    public function setResult(MatchResultDTO $result): JsonResponse
    {
        $this->matchService->saveResult($result);

        return $this->json(['result' => true]);
    }

    #[Route('/match/random', name: 'app_match_random')]
    public function randomResult(MatchResultDTO $result): JsonResponse
    {
        $this->matchService->randomResult($result);

        return $this->json(['result' => true]);
    }
}