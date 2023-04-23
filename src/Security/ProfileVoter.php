<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ProfileVoter extends Voter
{
    public const VIEW = 'view';

    public function __construct(
        private readonly Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        assert($subject instanceof User);

        return !$subject->isBanned() || $this->security->isGranted('ROLE_ADMIN');
    }
}