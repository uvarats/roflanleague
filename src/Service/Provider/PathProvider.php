<?php

declare(strict_types=1);

namespace App\Service\Provider;

final readonly class PathProvider
{
    public function __construct(
        private string $projectDir,
        private string $reportsStorage,
    ) {

    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    public function getReportsStorage(): string
    {
        return $this->reportsStorage;
    }
}