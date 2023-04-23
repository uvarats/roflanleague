<?php

declare(strict_types=1);

namespace App\Service\Report;

use App\Entity\MatchResult;
use App\Service\Provider\PathProvider;
use App\Service\Report\Interface\AbstractReportService;
use App\Service\Report\Interface\ReportableInterface;
use App\Service\Report\Util\PDFGenerator;
use League\Flysystem\FilesystemOperator;
use Twig\Environment;

final readonly class MatchReportService extends AbstractReportService
{
    public function __construct(
        PathProvider $pathProvider,
        PDFGenerator $pdf,
        FilesystemOperator $reportsStorage,
        private Environment $twig,
    )
    {
        parent::__construct($pathProvider->getReportsStorage(), $pdf, $reportsStorage);
    }

    public function getReport(MatchResult $result, bool $forceRegenerate = false): string
    {
        return $this->getReportInner($result, $forceRegenerate);
    }

    protected function getHtml(ReportableInterface $reportable): string
    {
        /** @var MatchResult $result */
        $result = $reportable;

        return $this->twig->render('report/match.html.twig', [
            'result' => $result,
        ]);
    }
}