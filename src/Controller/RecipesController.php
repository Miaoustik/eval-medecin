<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recettes')]
class RecipesController extends AbstractController
{
    #[Route(path: '', name: 'recipes_index')]
    public function index (RecipeRepository $repository): Response
    {
        $recipes = $repository->findAllPaginatedBy(maxResult: 10, property: ['title', 'id']);

        return $this->render('recipes/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
}