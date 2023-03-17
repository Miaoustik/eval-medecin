<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/Contact')]
class ContactController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    #[Route(path: '', name: 'contact_index')]
    public function index (): Response
    {
        return $this->render('contact/index.html.twig');
    }
}