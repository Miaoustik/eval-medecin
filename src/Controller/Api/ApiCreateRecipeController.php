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

        foreach ($ingredientsData as $value) {
            $ingredients[] = $value->getIngredient()->getName();
        }

        $ingredientsWithId = $this->ingredientRepository->findAllByName($ingredients);

        foreach ($ingredientsWithId as $ingredientWithId) {
            foreach ($ingredientsData as $newIngredient) {

                if ($newIngredient->getIngredient()->getName() === $ingredientWithId->getName()) {
                    if ($newIngredient->getIngredient()->getName() === 'Huile') {
                        //dd($newIngredient->getIngredient()->getName() === $ingredientWithId->getName());
                    }
                    $ingredientRecipe = (new IngredientRecipe())
                        ->setIngredient($ingredientWithId)
                        ->setQuantity($newIngredient->getQuantity());

                    $data->removeIngredientRecipe($newIngredient);
                    $data->addIngredientRecipe($ingredientRecipe);
                }
            }
        }
        return $data;
    }
}