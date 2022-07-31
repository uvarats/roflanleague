<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Form\BadgeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function main() {
        $badges = $this->em->getRepository(Badge::class);
        return $this->render('admin/badge/index.html.twig', [
            'badges' => $badges->findAll(),
        ]);
    }

    #[Route('/add', name: 'app_admin_badges_add')]
    public function add(Request $request) {
        $badge = new Badge();

        return $this->badgeAction($badge, $request);
    }
    #[Route('/edit/{id}', name: 'app_admin_badges_edit')]
    public function edit(Badge $badge, Request $request) {
        return $this->badgeAction($badge, $request, 'Редактирование бейджа');
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