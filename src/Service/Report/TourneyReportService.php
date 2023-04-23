<?php

declare(strict_types=1);

namespace App\Service\Report;

use App\Entity\Tourney;
use App\Exception\TourneyNotEndedException;
use App\Service\Provider\PathProvider;
use App\Service\Report\Interface\AbstractReportService;
use App\Service\Report\Interface\ReportableInterface;
use App\Service\Report\Util\PDFGenerator;
use App\Service\TourneyService;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Mpdf\MpdfException;
use Twig\Environment;

final readonly class TourneyReportService extends AbstractReportService
{
    public const REPORT_VIEW = 'report/tourney.html.twig';

    public function __construct(
        PathProvider $pathProvider,
        FilesystemOperator $reportsStorage,
        PDFGenerator $pdf,
        private TourneyService $tourneyService,
        private Environment $twig,
    ) {
        parent::__construct(
            projectDir: $pathProvider->getReportsStorage(),
            pdf: $pdf,
            reportsStorage: $reportsStorage
        );
    }

    /**
     * @param Tourney $tourney
     * @param bool $forceRegenerate
     * @return string
     * @throws FilesystemException
     * @throws MpdfException
     * @throws TourneyNotEndedException
     */
    public function getReport(Tourney $tourney, bool $forceRegenerate = false): string
    {
        if (!$tourney->isEnded()) {
            throw new TourneyNotEndedException(
                "Отчёты можно создавать только к завершенным турнирам."
            );
        }

        return $this->getReportInner($tourney, $forceRegenerate);
    }

    protected function getHtml(ReportableInterface $reportable): string
    {
        /** @var Tourney $tourney */
        $tourney = $reportable;

        $dates = $this->tourneyService->getTourneyDates($tourney);
        $finalRanks = $this->tourneyService->getFinalRanks($tourney);

        return $this->twig->render(self::REPORT_VIEW, [
            'tourney' => $tourney,
            'tourneyDates' => $dates,
            'finalRanks' => $finalRanks,
        ]);
    }
}