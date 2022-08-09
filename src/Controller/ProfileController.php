<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
        return $this->concreteProfile($user);
    }


    #[Route('/u/{id}', name: 'app_concrete_profile')]
    #[Entity('user', expr: 'repository.getFullUser(id)')]
    public function concreteProfile(User $user): Response
    {
        if ($user->isBanned() && !$this->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException("This user is banned.");
        }
        $users = $this->em->getRepository(User::class);
        $position = $users->getTopPosition($user);
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'position' => $position,
        ]);
    }
}
