<?php

namespace App\Controller\Api;

use App\Entity\IngredientRecipe;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ApiCreateRecipeController
{

    public function __construct(private readonly IngredientRepository $ingredientRepository)
    {
    }

    public function __invoke(Recipe $data)
    {
        $ingredientsData = $data->getIngredientRecipes();
        $ingredients = [];

        foreach ($ingredientsData as $key => $value) {
            $ingredients[] = $value->getIngredient()->getName();
        }

        $test = $this->ingredientRepository->findAllByName($ingredients);

        /** @var IngredientRecipe[] $ingredientsRecipes */
        $ingredientsRecipes = [];
        foreach ($test as $ingredient) {
            $ingredientRecipe = new IngredientRecipe();
            $ingredientRecipe->setIngredient($ingredient);
            $ingredientsRecipes[] = $ingredientRecipe;
        }

        $data->setIngredientRecipe($ingredientsRecipes);
        //TODO GET INgredients id

        dd($test, $data);
    }
}