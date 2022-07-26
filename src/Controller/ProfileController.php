<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
        return $this->concreteProfile($user);
    }

    #[Route('/profile/{id}', name: 'app_concrete_profile')]
    public function concreteProfile(User $user): Response
    {
        if ($user->isBanned() && !$this->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException("This user is banned.");
        }
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
