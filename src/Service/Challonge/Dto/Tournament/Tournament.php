<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto\Tournament;

use App\Service\Challonge\Dto\AbstractData;
use App\Service\Challonge\Dto\Links;

class Tournament extends AbstractData
{
    public function __construct(
        public readonly string     $id,
        public readonly string     $type,
        public readonly Attributes $attributes,
        public readonly ?Links     $links = null,
    )
    {
    }
}