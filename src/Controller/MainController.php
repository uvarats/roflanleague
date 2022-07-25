<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use http\Client;
use PhpParser\Node\Expr\Cast\Double;
use Psr\Http\Client\ClientInterface;
use Reflex\Challonge\Challonge;
use Reflex\Challonge\DTO\MatchDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MainController extends AbstractController
{

    public function __construct(
        private MailerInterface $mailer,
        private TokenStorageInterface $tokenStorage,
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $http = new \GuzzleHttp\Client();
        $challonge = new Challonge($http, 'CksgsGocPPx5fCAo0sbSsh3aMHnJye1lcNYgzGeN', true);
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/rating/{page}', name: 'app_rating')]
    public function rating(int $page = 1) {
        $users = $this->em->getRepository(User::class);
        /** @var User[] $top */
        $top = $users->getRatingTop($page);

        return $this->render('main/rating.html.twig', [
            'users' => $top,
            'page' => $page,
        ]);
    }
}
