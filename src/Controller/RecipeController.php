<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recette')]
class RecipeController extends AbstractController
{
    #[Route(path: '/{id}', name: 'recipe_index')]
    public function index(int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->findByIdRecipe($id);

        return $this->render('recipe/index.html.twig', [
            'recipe' => $recipe
        ]);
    }
}