<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Enum\ParticipantAction;
use App\Entity\Enum\TournamentType;
use App\Entity\Enum\TourneyState;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use App\Form\TourneyType;
use App\Service\ChallongeService;
use App\Service\TourneyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class TourneyAdminController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ChallongeService       $challonge,
        private readonly TourneyService         $tourneyService
    )
    {

    }

    #[Route('/tourneys', name: 'app_tourneys')]
    public function tourneys(): Response
    {
        $tourneys = $this->em->getRepository(Tourney::class);
        return $this->render('admin/tourney/tourneys.html.twig', [
            'tourneys' => $tourneys->findAll(),
        ]);
    }

    #[Route('/tourneys/add', name: 'app_tourney_add')]
    public function newTourney(Request $request): RedirectResponse|Response
    {
        $tourney = new Tourney();
        $form = $this->createForm(TourneyType::class, $tourney);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $challongeTourney = $this->challonge
                ->createTournament($tourney->getName(), TournamentType::DOUBLE_ELIMINATION);

            $tourney->setChallongeUrl($challongeTourney->url);

            $this->em->persist($tourney);
            try {
                $this->em->flush();
            } catch (\Throwable $throwable) {
                $this->challonge->removeTournament($challongeTourney->url);
            }

            return $this->redirectToRoute('app_tourneys');
        }

        // using one template for adding and editing
        return $this->render('admin/tourney/tourney_add.html.twig', [
            'tourneyForm' => $form,
            'title' => "Создание турнира",
        ]);
    }

    #[Route('/tourney/{id}/edit', name: 'app_tourney_edit')]
    public function changeTourney(Tourney $tourney, Request $request): Response
    {
        $form = $this->createForm(TourneyType::class, $tourney);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_tourneys');
        }

        // using one template for adding and editing
        return $this->render('admin/tourney/tourney_add.html.twig', [
            'tourneyForm' => $form,
            'title' => 'Редактирование турнира',
        ]);
    }

    #[Route('/tourney/{id}/remove', name: 'app_tourney_remove', methods: ['POST'])]
    public function removeTourney(Tourney $tourney): JsonResponse
    {
        $this->challonge->removeTournament($tourney->getChallongeUrl());
        $this->em->remove($tourney);
        $this->em->flush();
        return new JsonResponse([
            'success' => true,
        ]);
    }

    #[Route('/tourney/{id}/start', name: 'app_tourney_start', methods: ['POST'])]
    public function startTourney(Tourney $tourney): JsonResponse
    {
        $this->challonge->startTournament($tourney);
        $tourney->setState(TourneyState::STARTED->value);
        $this->em->flush();

        return $this->json(['success' => 'Турнир успешно запущен!']);
    }

    #[Route('/tourney/{id}/reset', name: 'app_tourney_reset', methods: ['POST'])]
    public function resetTourney(Tourney $tourney): JsonResponse
    {
        $this->challonge->resetTournament($tourney);

        $tourney->setState(TourneyState::NEW->value);
        $results = $this->em->getRepository(MatchResult::class);
        $tourneyResults = $results->findBy(['tourney' => $tourney]);

        foreach ($tourneyResults as $tourneyResult) {
            $this->em->remove($tourneyResult);
        }
        $this->em->flush();

        return $this->json(['message' => 'Турнир успешно сброшен!']);
    }

    #[Route('/tourney/{id}/randomize', name: 'app_tourney_randomize', methods: ['POST'])]
    public function randomizeParticipants(Tourney $tourney): JsonResponse
    {
        if ($tourney->getState() !== TourneyState::NEW->value) {
            throw new \InvalidArgumentException(
                'Только у нового турнира можно перемешивать участников!'
            );
        }

        $this->challonge->randomizeParticipants($tourney);

        return $this->json([
            'success' => "Участники успешно перемешаны!",
        ]);
    }

    #[Route('/tourney/{id}/end', name: 'app_tourney_end', methods: ['POST'])]
    public function endTourney(Tourney $tourney): JsonResponse
    {
        if ($tourney->getState() !== TourneyState::STARTED->value) {
            return $this->json(['error' => 'Турнир не запущен']);
        }

        $this->tourneyService->recalculateRating($tourney);
        $this->challonge->endTournament($tourney);
        $tourney->setState(TourneyState::ENDED->value);
        $this->em->flush();

        return $this->json(['success' => "Турнир завершен!"]);
    }

    #[Route('/tourney/{id}/participants', name: 'app_tourney_participants')]
    public function tourneyParticipants(int $id): Response
    {
        $tourneys = $this->em->getRepository(Tourney::class);
        $tourney = $tourneys->getWithParticipants($id);

        return $this->render('admin/tourney/tourney_participants.html.twig', [
            'tourney' => $tourney,
        ]);
    }

    #[Route('/tourney/{id}/participants/add', name: 'app_tourney_participants_add')]
    public function tourneyAddParticipants(Tourney $tourney): Response
    {
        $users = $this->em->getRepository(User::class);
        $availableUsers = $users->getUsersNotInTourney($tourney);
        return $this->render('admin/tourney/tourney_participants_add.html.twig', [
            'tourney' => $tourney,
            'firstAvailable' => $availableUsers,
        ]);
    }

    #[Route('/tourney/{id}/add-participant', name: 'app_tourney_user_add')]
    public function tourneyAddSingleParticipant(Tourney $tourney, Request $request): JsonResponse
    {
        return $this->participantsAction($tourney, $request, ParticipantAction::ADD);
    }

    #[Route('/tourney/{id}/remove-participant', name: 'app_tourney_user_remove')]
    public function removeParticipant(Tourney $tourney, Request $request): JsonResponse
    {
        return $this->participantsAction($tourney, $request, ParticipantAction::REMOVE);
    }

    public function participantsAction(Tourney $tourney, Request $request, ParticipantAction $action): JsonResponse
    {
        $id = $request->request->get('id');
        if ($id) {
            $users = $this->em->getRepository(User::class);
            $user = $users->find($id);
            if ($user !== null) {
                if ($action === ParticipantAction::ADD) {
                    $this->challonge->addParticipant($tourney, $user);
                    $tourney->addParticipant($user);
                }

                if ($action === ParticipantAction::REMOVE) {
                    $this->challonge->removeParticipant($tourney, $user);
                    $tourney->removeParticipant($user);
                }

                $this->em->flush();
                return $this->json([
                    'success' => true,
                ]);
            }
            return $this->json([
                'error' => 'User with this id does not exists!',
            ]);
        }

        return $this->json([
            'error' => 'Please, provide id...',
        ]);
    }

    #[Route('/tourney/{id}/participants/get', name: 'app_tourney_participants_get', methods: ["POST"])]
    public function getAdditionalParticipants(Tourney $tourney, Request $request): JsonResponse
    {
        $page = $request->request->get('page');
        if ($page) {
            $users = $this->em->getRepository(User::class);
            $additionalUsers = $users->getUsersNotInTourney($tourney, $page);
            $result = array_map(static function (User $target) {
                return [
                    'id' => $target->getId(),
                    'name' => $target->getUsername()
                ];
            }, $additionalUsers);
            return $this->json($result);
        }

        return new JsonResponse([
            'error' => 'Page is not provided :/',
        ]);
    }
}
