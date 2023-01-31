<?php

declare(strict_types=1);

namespace App\Service\Interface;

interface UrlQueryConvertable
{
    public function toUrlQueryParams(): string;
}