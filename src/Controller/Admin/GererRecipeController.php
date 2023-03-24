<?php

namespace App\Controller\Admin;

use App\Repository\RecipeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/gerer-recette')]
#[IsGranted('ROLE_ADMIN')]
class GererRecipeController extends AbstractController
{
    #[Route(path: '', name: 'admin_gererRecipe_index')]
    public function index (RecipeRepository $repository, Request $request): Response
    {

        $maxResultPerPage = 10;

        $queryPage = $request->query->get('page') ?? 1;

        $recipes = $repository->findAllPaginatedBy(maxResult: $maxResultPerPage, page: $queryPage, property: ['title', 'id']);
        $pageNumber = ceil(($repository->count([])) / $maxResultPerPage);


        return $this->render('/admin/gererRecipe/index.html.twig', [
            'recipes' => $recipes,
            'pageNumber' => $pageNumber,
            'currentPage' => $queryPage,
            'paginationPath' => 'admin_gererRecipe_index'
        ]);
    }
}