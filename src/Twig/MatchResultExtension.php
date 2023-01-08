<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\MatchResult;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MatchResultExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('opponent', [$this, 'getOpponent']),
            new TwigFilter('result', [$this, 'getResultForUser']),
            new TwigFilter('form', [$this, 'getForm']),
        ];
    }

    public function getOpponent(MatchResult $matchResult, User $user): ?User
    {
        $firstUser = $matchResult->getHomePlayer();
        $secondUser = $matchResult->getAwayPlayer();

        if ($firstUser === $user) {
            return $secondUser;
        }

        if ($secondUser === $user) {
            return $firstUser;
        }

        throw new BadRequestHttpException("No matched opponent!");
    }

    public function getResultForUser(MatchResult $matchResult, User $user): string
    {
        $winner = $matchResult->getWinner();
        $loser = $matchResult->getLoser();

        if ($winner === $user) {
            return "<span class='fw-bold text-success'>Победа</span>";
        }

        if ($loser === $user) {
            return "<span class='fw-bold text-danger'>Поражение</span>";
        }

        return "<span class='text-secondary'>Неизвестно</span>";
    }

    /**
     * @param MatchResult[] $results
     * @param User $user
     * @return string|null
     */
    public function getForm(array $results, User $user): ?string
    {
        $formWidget = "";

        foreach ($results as $result) {
            if ($result->isWinner($user)) {
                $formWidget .= "<span class='badge bg-success'>В</span>" . PHP_EOL;
            } else {
                $formWidget .= "<span class='badge bg-danger'>П</span>" . PHP_EOL;
            }
        }

        return $formWidget;
    }
}