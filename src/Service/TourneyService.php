<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tourney;
use App\Form\TourneyType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;

readonly class TourneyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChallongeService $challonge,
        private LoggerInterface $logger,
    )
    {
    }

    public function createTourney(Tourney $tourney, FormInterface $form): ?Tourney
    {
        $challongeTourney = $this->challonge
            ->createTournament($tourney->getName(), $form->get('type')->getNormData());

        $tourney->setChallongeUrl($challongeTourney->url);

        $this->entityManager->persist($tourney);
        try {
            $this->entityManager->flush();
        } catch (\Throwable $throwable) {
            $this->challonge->removeTournament($challongeTourney->url);
            $this->logger->error("Error on tourney create: {$throwable->getMessage()}");
            return null;
        }

        return $tourney;
    }
}