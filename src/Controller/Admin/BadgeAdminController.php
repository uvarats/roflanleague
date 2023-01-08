<?php

namespace App\Controller\Admin;

use App\Entity\Badge;
use App\Form\BadgeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/badge')]
class BadgeAdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('', name: 'app_admin_badges')]
    public function main(): Response
    {
        $badges = $this->em->getRepository(Badge::class);
        return $this->render('admin/badge/index.html.twig', [
            'badges' => $badges->findAll(),
        ]);
    }

    #[Route('/add', name: 'app_admin_badges_add')]
    public function add(Request $request): RedirectResponse|Response
    {
        $badge = new Badge();

        return $this->badgeAction($badge, $request);
    }

    #[Route('/edit/{id}', name: 'app_admin_badges_edit')]
    public function edit(Badge $badge, Request $request): RedirectResponse|Response
    {
        return $this->badgeAction($badge, $request, 'Редактирование бейджа');
    }

    #[Route('/remove-badge', name: 'app_admin_badge_remove', methods: [ 'POST' ])]
    public function removeBadge(Request $request): JsonResponse
    {
        $id = $request->request->get('id');

        $errorMsg = "Please, provide an id to request";

        if ($id) {
            $badges = $this->em->getRepository(Badge::class);
            $badge = $badges->find($id);
            if($badge !== null) {
                $this->em->remove($badge);
                $this->em->flush();
                return $this->json([
                    "success" => true,
                ]);
            }
            $errorMsg = "Badge object not found by this id";
        }

        return $this->json([
            "error" => $errorMsg,
        ]);
    }

    public function badgeAction(
        Badge $badge,
        Request $request,
        string $pageTitle = "Добавление бейджа"
    ): RedirectResponse|Response
    {
        $form = $this->createForm(BadgeType::class, $badge);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($badge);
            $this->em->flush();

            return $this->redirectToRoute('app_admin_badges');
        }

        return $this->renderForm('admin/badge/add.html.twig', [
            'badgeForm' => $form,
            'pageTitle' => $pageTitle,
        ]);
    }
}