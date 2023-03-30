<?php

namespace App\Controller\Admin\GererAllergen;

use App\Repository\AllergenRepository;
use App\Traits\PaginateTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/gerer-les-allergies')]
#[IsGranted('ROLE_ADMIN')]
class GererAllergenController extends AbstractController
{

    use PaginateTrait;

    #[Route(path: '', name: 'admin_gererAllergen_index')]
    public function index (AllergenRepository $repository, Request $request): Response
    {
        $pagination = $this->paginate($request, $repository, 'admin_gererAllergen_index', 10, 'name');

        return $this->render('/admin/gererAllergen/index.html.twig', [
            ...$pagination
        ]);
    }

}