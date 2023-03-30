<?php

namespace App\Controller\Admin\GererRecette;

use App\Repository\RecipeRepository;
use App\Traits\PaginateTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/gerer-les-recettes')]
#[IsGranted('ROLE_ADMIN')]
class GererRecipeController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '', name: 'admin_gererRecipe_index')]
    public function index (RecipeRepository $repository, Request $request): Response
    {

       $pagination = $this->paginate($request, $repository, 'admin_gererRecipe_index', null, null, ['title', 'id']);

        return $this->render('/admin/gererRecipe/index.html.twig', [
            ...$pagination
        ]);
    }
}