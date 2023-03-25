<?php

namespace App\Controller\Api;

use App\Entity\Ingredient;
use App\Entity\IngredientRecipe;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ApiModifyRecipeController
{

    public function __construct(private readonly IngredientRepository $ingredientRepository)
    {
    }

    public function __invoke(Recipe $data)
    {
        $ingredientsData = $data->getIngredientRecipes();
        $ingredients = [];

        foreach ($ingredientsData as $value) {
            $ingredients[] = [
                $value->getIngredient()->getName(),
                $value->getQuantity()
            ];
        }

        $ingredientsWithId = $this->ingredientRepository->findAllByName($ingredients);
        //dd($ingredientsData, $ingredientsWithId);
        foreach ($ingredientsWithId as $ingredientWithId) {
            foreach ($ingredientsData as $newIngredient) {
                $data->removeIngredientRecipe($newIngredient);

                if ($newIngredient->getIngredient()->getName() === $ingredientWithId->getName()) {

                    $ingredientRecipe = (new IngredientRecipe())
                        ->setIngredient($ingredientWithId)
                        ->setQuantity($newIngredient->getQuantity())
                        ->setRecipe($data);


                    $ingredients = array_filter($ingredients, function ($value) use ($newIngredient) {
                        return $value[0] !== $newIngredient->getIngredient()->getName();
                    });
                    $data->addIngredientRecipe($ingredientRecipe);
                }
            }
        }

        foreach ($ingredients as $ingredient) {
            $ingredientNew = (new Ingredient())
                ->setName($ingredient[0]);
            $newIngredientRecipe = (new IngredientRecipe())
                ->setRecipe($data)
                ->setQuantity($ingredient[1])
                ->setIngredient($ingredientNew);

            $data->addIngredientRecipe($newIngredientRecipe);
        }


        //dd($data, $ingredients, $ingredientsData);
        return $data;
    }
}