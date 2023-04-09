<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
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
        /** @var User $user */
        $user = $this->getUser();

        $recipe = $repository->findByIdRecipe($id, !$user);

        if (!$recipe) {
            return new Response(status: 404);
        }

        if ($recipe->isPatientOnly() === true && !$user) {
            return new Response(status: 404);
        }

        if ($user) {
            $recipeAllergens =  $recipe->getAllergens();
            $recipeDiets = $recipe->getDiets();

            foreach ($user->getAllergens() as $userAllergen) {
                foreach($recipeAllergens as $recipeAllergen) {
                    if ($recipeAllergen->getId() === $userAllergen->getId()) {
                        return new Response(status: 404);
                    }
                }
            }

            $userDiets = $user->getDiets();
            foreach( $userDiets as $userDiet) {
                if (count($recipeDiets) === 0 && count($userDiets) > 0) {
                    return new Response(status: 404);
                }

                foreach ($recipeDiets as $recipeDiet) {
                    if ($recipeDiet->getId() !== $userDiet->getId()) {
                        return new Response(status: 404);
                    }
                }
            }
        }


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