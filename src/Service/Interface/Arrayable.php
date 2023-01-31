<?php

declare(strict_types=1);

namespace App\Service\Interface;

interface Arrayable
{
    public function toArray(): array;
}