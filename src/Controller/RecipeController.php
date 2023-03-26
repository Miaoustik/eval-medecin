<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/recette')]
class RecipeController extends AbstractController
{

    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    #[Route(path: '/{id}', name: 'recipe_index')]
    public function index(int $id, RecipeRepository $repository, DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        $recipe = $repository->findByIdRecipe($id);
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();


        return $this->render('recipe/index.html.twig', [
            'recipe' => $recipe,
            'diets' => $diets,
            'allergens' => $allergens
        ]);
    }

    #[Route(path: '/api/{id}', name: 'recipe_api_get')]
    public function ApiGet (Recipe $recipe): Response
    {
        return new JsonResponse($this->serializer->serialize($recipe, JsonEncoder::FORMAT, ['groups' => 'MODIFY_RECIPE']), 200, [
            'Content-Type' => 'application/json',
        ], true);
    }
}