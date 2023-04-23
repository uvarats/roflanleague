<?php

declare(strict_types=1);

namespace App\Service\Report\Util;

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

final readonly class PDFGenerator
{

    /**
     * @throws MpdfException
     */
    public function generate(string $html, string $path): void
    {
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output($path, Destination::FILE);
    }

}