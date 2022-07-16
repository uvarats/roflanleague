<?php

namespace App\Controller;

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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(MailerInterface $mailer): Response
    {
        $http = new \GuzzleHttp\Client();
        $challonge = new Challonge($http, 'CksgsGocPPx5fCAo0sbSsh3aMHnJye1lcNYgzGeN', true);


        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
