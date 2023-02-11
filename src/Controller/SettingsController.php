<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(#[CurrentUser] User $user): Response
    {
        return $this->render('settings/index.html.twig', [
            'user' => $user,
        ]);
    }
}
