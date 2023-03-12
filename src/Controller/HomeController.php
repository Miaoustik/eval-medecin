<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '')]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', ['hello' => 'testing']);
    }
}