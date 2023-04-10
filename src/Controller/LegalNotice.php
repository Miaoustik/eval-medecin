<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '')]
class LegalNotice extends AbstractController
{
    #[Route(path: '/mentions-legales', name: 'mentions')]
    public function mentions (): Response
    {
        return $this->render('mentions-legales.html.twig');
    }
}