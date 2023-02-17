<?php

declare(strict_types=1);

namespace App\Service\Challonge\Dto\User;

use App\Service\Challonge\Dto\AbstractData;

class UserAttributes extends AbstractData
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $username = null,
        public readonly ?string $imageUrl = null,
    )
    {
    }
}