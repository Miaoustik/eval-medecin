<?php

namespace App\Controller\Admin\GererDiet;

use App\Repository\DietRepository;
use App\Traits\PaginateTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/gerer-les-regimes')]
#[IsGranted('ROLE_ADMIN')]
class GererDietController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '', name: 'admin_gererDiet_index')]
    public function index (DietRepository $repository, Request $request): Response
    {
        $pagination = $this->paginate($request, $repository, 'admin_gererDiet_index', 10, 'name');

        return $this->render('/admin/gererDiet/index.html.twig', [
            ...$pagination
        ]);
    }


}