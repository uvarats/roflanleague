<?php

declare(strict_types=1);

namespace App\Collection;

use App\Entity\Tourney;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Tourney>
 *
 * @implements \IteratorAggregate<Tourney>
 */
final class TourneyCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Tourney::class;
    }
}