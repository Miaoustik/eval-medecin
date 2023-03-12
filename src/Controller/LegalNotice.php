<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/LegalNotice')]
class LegalNotice extends AbstractController
{
    #[Route(path: '')]
    public function index (): Response
    {
        return new Response();
    }
}