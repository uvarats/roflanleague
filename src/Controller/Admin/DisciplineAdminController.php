<?php

namespace App\Controller\Admin;

use App\Entity\Discipline;
use App\Form\DisciplineType;
use App\Repository\DisciplineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/discipline')]
#[IsGranted('ROLE_ADMIN')]
class DisciplineAdminController extends AbstractController
{
    #[Route('', name: 'app_admin_discipline_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_disciplines');
    }

    #[Route('/new', name: 'app_admin_discipline_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DisciplineRepository $disciplineRepository): Response
    {
        $discipline = new Discipline();
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $disciplineRepository->save($discipline, true);

            return $this->redirectToRoute('app_admin_discipline_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/discipline/new.html.twig', [
            'discipline' => $discipline,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_discipline_admin_show', methods: ['GET'])]
    public function show(Discipline $discipline): Response
    {
        return $this->render('admin/discipline/show.html.twig', [
            'discipline' => $discipline,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_discipline_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Discipline $discipline, DisciplineRepository $disciplineRepository): Response
    {
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $disciplineRepository->save($discipline, true);

            return $this->redirectToRoute('app_admin_discipline_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/discipline/edit.html.twig', [
            'discipline' => $discipline,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_discipline_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Discipline $discipline, DisciplineRepository $disciplineRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$discipline->getId(), $request->request->get('_token'))) {
            $disciplineRepository->remove($discipline, true);
        }

        return $this->redirectToRoute('app_admin_discipline_index', [], Response::HTTP_SEE_OTHER);
    }
}
