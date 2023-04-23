<?php

declare(strict_types=1);

namespace App\Service\Report\Interface;

interface ReportableInterface
{
    public function getReportName(): string;
}