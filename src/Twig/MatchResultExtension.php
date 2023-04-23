<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Enum\Result;
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
            new TwigFilter('opponent', function (MatchResult $matchResult, User $user): ?User {
                return $this->getOpponent($matchResult, $user);
            }),
            new TwigFilter('result', function (MatchResult $matchResult, User $user): string {
                return $this->getResultForUser($matchResult, $user);
            }),
            new TwigFilter('form', function (array $results, User $user): ?string {
                return $this->getForm($results, $user);
            }),
            new TwigFilter('winner', function (MatchResult $matchResult): string {
                return $this->getWinner($matchResult);
            })
        ];
    }

    public function getWinner(MatchResult $matchResult): string {
        $result = $matchResult->getResult();
        $homePlayer = $matchResult->getHomePlayer();
        $awayPlayer = $matchResult->getAwayPlayer();

        return match($result) {
            Result::FIRST_WIN => $homePlayer->getUsername(),
            Result::SECOND_WIN => $awayPlayer->getUsername(),
            Result::TIE => 'Ничья',
            Result::CANCELED => 'Отменен',
        };
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