<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recettes')]
class RecipesController extends AbstractController
{
    #[Route(path: '', name: 'recipes_index')]
    public function index (RecipeRepository $repository, Request $request): Response
    {
        $maxResultPerPage = 10;

        $queryPage = $request->query->get('page') ?? 1;

        $recipes = $repository->findAllPaginatedBy(maxResult: $maxResultPerPage, page: $queryPage, property: ['title', 'id']);
        $pageNumber = ceil(($repository->count([])) / $maxResultPerPage);


        return $this->render('recipes/index.html.twig', [
            'recipes' => $recipes,
            'pageNumber' => $pageNumber,
            'currentPage' => $queryPage,
            'paginationPath' => 'recipes_index'
        ]);
    }
}