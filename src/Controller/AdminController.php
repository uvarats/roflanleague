<?php

namespace App\Controller;

use App\Entity\Tourney;
use App\Entity\User;
use App\Form\TourneyType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Framework\RequestConfig;

#[Route('/admin')]
class AdminController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }
    #[Route('', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/users/{page}', name: 'app_admin_users')]
    public function users(int $page = 1): Response
    {
        $users = $this->em->getRepository(User::class);
        $usersPerPage = 30;
        $count = $users->getCount();
        return $this->render('admin/users.html.twig', [
            'currentPage' => $page,
            'pagesCount' => ceil($count / $usersPerPage),
            'users' => $users->getListByPage($page, $usersPerPage),
        ]);
    }

    #[Route('/users/manage/{id}', name: 'app_admin_manage_user')]
    public function manage(User $user): Response
    {
        return $this->render('admin/manage_user.html.twig', [
            'user' => $user,
        ]);
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

        return $this->processTourney($tourney, $request, "Добавление турнира");
    }

    #[Route('/tourney/edit/{id}', name: 'app_tourney_edit')]
    public function changeTourney(Tourney $tourney, Request $request): Response
    {
        return $this->processTourney($tourney, $request,  "Изменение турнира", false);
    }

    #[Route('/tourney/remove', name: 'app_tourney_remove', methods: ['POST'])]
    public function removeTourney(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        $id = $data->id;
        $tourney = $this->em->getRepository(Tourney::class)->find($id);
        if ($tourney) {
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

    public function processTourney(Tourney $tourney, Request $request, string $pageTitle, bool $needsPersist = true): RedirectResponse|Response
    {
        $form = $this->createForm(TourneyType::class, $tourney);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            if ($needsPersist) {
                $this->em->persist($tourney);
            }
            $this->em->flush();

            return $this->redirectToRoute('app_tourneys');
        }
        // using one template for adding and editing
        return $this->renderForm('admin/tourney/tourney_add.html.twig', [
            'tourneyForm' => $form,
            'title' => $pageTitle,
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
    public function tourneyAddParticipants(Tourney $tourney) {
        $users = $this->em->getRepository(User::class);
        $availableUsers = $users->getUsersNotInTourney($tourney);
        return $this->render('admin/tourney/tourney_participants_add.html.twig', [
            'tourney' => $tourney,
            'firstAvailable' => $availableUsers,
        ]);
    }
}
