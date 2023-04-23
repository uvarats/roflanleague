<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Service\Report\MatchReportService;
use App\Service\Report\TourneyReportService;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report')]
final class ReportController extends AbstractController
{
    public function __construct(
        private readonly FilesystemOperator $reportsStorage,
    ) {
    }

    #[Route('/tourney/{id}', name: 'app_report_tourney')]
    public function tourneyReport(
        Tourney $tourney,
        Request $request,
        TourneyReportService $reportService,

    ): StreamedResponse {
        $force = filter_var(
            $request->query->get('force', false),
            FILTER_VALIDATE_BOOL
        );

        $filename = $reportService->getReport($tourney, $force);

        return $this->streamResponse($filename);
    }

    #[Route('/match/{id}', name: 'app_report_match')]
    public function matchReport(
        MatchResult $matchResult,
        Request $request,
        MatchReportService $reportService,
    ): StreamedResponse {
        $force = filter_var(
            $request->query->get('force', false),
            FILTER_VALIDATE_BOOL
        );

        $filename = $reportService->getReport($matchResult, $force);

        return $this->streamResponse($filename);
    }

    private function streamResponse(string $filename): StreamedResponse
    {
        $reportsStorage = $this->reportsStorage;

        $response = new StreamedResponse();
        $response->setCallback(static function () use ($filename, $reportsStorage) {
            $output = fopen('php://output', 'wb');
            $content = $reportsStorage->readStream($filename);

            stream_copy_to_stream($content, $output);
        });

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Type', $reportsStorage->mimeType($filename));
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}