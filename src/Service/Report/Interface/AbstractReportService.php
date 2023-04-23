<?php

declare(strict_types=1);

namespace App\Service\Report\Interface;

use App\Service\Report\Util\PDFGenerator;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Mpdf\MpdfException;

abstract readonly class AbstractReportService implements ReportServiceInterface
{
    public function __construct(
        private string $projectDir,
        private PDFGenerator $pdf,
        private FilesystemOperator $reportsStorage,
    ) {
    }

    /**
     * @throws MpdfException
     * @throws FilesystemException
     */
    protected function getReportInner(ReportableInterface $reportable, bool $forceRegenerate = false): string
    {
        $filename = $this->getFilename($reportable);

        if ($this->reportsStorage->fileExists($filename) && !$forceRegenerate) {
            return $filename;
        }

        return $this->generateReport($reportable);
    }

    /**
     * @throws MpdfException
     */
    protected function generateReport(ReportableInterface $reportable): string
    {
        $html = $this->getHtml($reportable);

        $path = $this->getPath($reportable);
        $this->pdf->generate($html, $path);

        return $this->getFilename($reportable);
    }

    abstract protected function getHtml(ReportableInterface $reportable): string;

    protected function getFilename(ReportableInterface $reportable): string
    {
        return $reportable->getReportName();
    }

    protected function getPath(ReportableInterface $reportable): string
    {
        $filename = $reportable->getReportName();

        return $this->projectDir . '/' . $filename;
    }
}