<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoticeController extends AbstractController
{
    #[Route(path: '/recette/{recipeid}/avis')]
    public function getNotices ($recipeid): Response
    {

    }
}