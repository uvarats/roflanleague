<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto\User;

use App\Service\Challonge\Dto\AbstractData;

class User extends AbstractData
{
    public function __construct(
        public readonly ?string         $id = null,
        public readonly ?string         $type = null,
        public readonly ?UserAttributes $attributes = null,
    )
    {
    }
}