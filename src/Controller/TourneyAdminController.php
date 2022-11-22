<?php

namespace App\Controller;

use App\Entity\ParticipantAction;
use App\Entity\TournamentType;
use App\Entity\Tourney;
use App\Entity\User;
use App\Form\TourneyType;
use App\Service\ChallongeService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;
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
        private EntityManagerInterface $em,
        private ChallongeService       $challonge
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

            $tourney->setChallongeId($challongeTourney->id);

            $this->em->persist($tourney);
            try {
                $this->em->flush();
            } catch (\Throwable $exception) {
                $this->challonge->removeTournament($challongeTourney->id);
            }

            return $this->redirectToRoute('app_tourneys');
        }
        // using one template for adding and editing
        return $this->renderForm('admin/tourney/tourney_add.html.twig', [
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
        return $this->renderForm('admin/tourney/tourney_add.html.twig', [
            'tourneyForm' => $form,
            'title' => 'Редактирование турнира',
        ]);
    }

    #[Route('/tourney/remove', name: 'app_tourney_remove', methods: ['POST'])]
    public function removeTourney(Request $request): JsonResponse
    {
        $id = $request->request->get('id');
        $tourney = $this->em->getRepository(Tourney::class)->find($id);
        if ($tourney) {
            $this->challonge
                ->removeTournament($tourney->getChallongeId());
            $this->em->remove($tourney);
            $this->em->flush();
            return new JsonResponse([
                'success' => true,
            ]);
        }
        return new JsonResponse([
            'error' => "Object doesn't exists",
        ]);
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
            if ($user) {
                if ($action === ParticipantAction::ADD &&
                    $this->challonge->addParticipant($tourney->getChallongeId(), $user)
                ) {
                    $tourney->addParticipant($user);
                }

                if ($action === ParticipantAction::REMOVE) {
                    $this->challonge->removeParticipant($tourney->getChallongeId(), $user);
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
            $result = array_map(function (User $target) {
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
