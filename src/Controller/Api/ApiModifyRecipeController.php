<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ApiModifyRecipeController
{
    public function __invoke(Recipe $data)
    {

        $removes = $data->getIngredientRecipes();

        foreach ($removes as $remove) {

        }

        dd($data);
    }
}