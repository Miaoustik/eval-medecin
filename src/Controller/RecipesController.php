<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Traits\PaginateTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recettes')]
class RecipesController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '', name: 'recipes_index')]
    public function index (RecipeRepository $repository, Request $request): Response
    {

        $paginationParams = $this->paginateWithSearch(
            request: $request,
            repository: $repository,
            searchBy: 'title',
            order: 'title',
            property: ['title', 'id']
        );


        return $this->render('recipes/index.html.twig', [
            ...$paginationParams
        ]);
    }
}