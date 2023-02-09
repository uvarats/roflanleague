<?php

declare(strict_types=1);

namespace App\Service\Trait;

trait ArrayableTrait
{
    public function toArray(): array {
        return get_object_vars($this);
    }
}