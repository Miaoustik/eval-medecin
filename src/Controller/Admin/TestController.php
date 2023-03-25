<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Entity\IngredientRecipe;
use App\Entity\Recipe;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/test')]
class TestController extends AbstractController
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }


    #[Route(path: '', name: 'test_index', methods: ['POST'])]
    public function index (
        Request $request,
        RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository,
        DietRepository $dietRepository,
        AllergenRepository $allergenRepository,
        EntityManagerInterface $entityManager
    ):Response
    {
        $data = json_decode($request->getContent(), JSON_UNESCAPED_UNICODE);


        $id = explode("/", $data['@id']);

        $recipe = $recipeRepository->findTest(end($id));

        $allergenIds = [];
        foreach( $data['allergens'] as $allergen) {
            $allergenId = explode('/', $allergen);
            $allergenId = end($allergenId);
            $allergenIds[] = $allergenId;
        }
        $allergens = $allergenRepository->findByIds($allergenIds);

        foreach ($allergens as $allergen) {
            if (!in_array($allergen->getId(), $allergenIds)) {
                $recipe->removeAllergen($allergen);
            }
        }

        $dietsIds = [];
        foreach( $data['diets'] as $diet) {
            $dietId = explode('/', $diet);
            $dietId = end($dietId);
            $dietsIds[] = $dietId;
        }

        $diets = $dietRepository->findByIds($dietsIds);

        foreach ($diets as $diet) {
            if (!in_array($diet->getId(), $dietsIds)) {
                $recipe->removeDiet($diet);
            }
        }

        $recipe->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setPreparationTime($data['preparationTime'])
            ->setBreakTime($data['breakTime'])
            ->setCookingTime($data['cookingTime']);


        $ingredientsRecipes = $recipe->getIngredientRecipes();
        $ingredients = array_map(function ($e) {
            return [
                $e['ingredient']['name'],
                $e['quantity']
            ];
        }, $data['ingredientRecipes']);

        foreach($ingredientsRecipes as $index => $ingredientsRecipe) {
            if (!isset($ingredients[$index])) {
                $recipe->removeIngredientRecipe($ingredientsRecipe);
            }
        }

        $ingredientsWithId = $ingredientRepository->findAllByName($ingredients);

        $ingredients = array_map(function ($e) use ($ingredientsWithId) {
            foreach ($ingredientsWithId as $ingredientWithId) {
                if ($ingredientWithId->getName() === $e[0]) {
                    $e[0] = $ingredientWithId;
                    return $e;
                }
            }
            return $e;
        }, $ingredients);

        foreach ($ingredients as $index => $ingredient) {
            if (isset($ingredientsRecipes[$index])) {
                if ($ingredient[0] instanceof Ingredient) {
                    $ingredientsRecipes[$index]->setIngredient($ingredient[0]);

                } else {
                    $newIngredient = (new Ingredient())
                        ->setName($ingredient[0]);
                    $ingredientsRecipes[$index]->setIngredient($newIngredient);
                }
                $ingredientsRecipes[$index]->setQuantity($ingredient[1]);
            } else {
                $newIngredientRecipe = new IngredientRecipe();
                if ($ingredient[0] instanceof Ingredient) {
                    $newIngredientRecipe->setIngredient($ingredient[0]);

                } else {
                    $newIngredient = (new Ingredient())
                        ->setName($ingredient[0]);
                    $newIngredientRecipe->setIngredient($newIngredient);
                }
                $newIngredientRecipe->setQuantity($ingredient[1]);
                $recipe->addIngredientRecipe($newIngredientRecipe);
            }
        }

        $recipe->setStages($data['stages']);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(data: json_encode($e, JSON_UNESCAPED_UNICODE), status: 500 , json: true);
        }

        $recipeJson = $this->serializer->serialize($data, Recipe::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $recipe]);

        return new JsonResponse(data: json_encode($recipe, JSON_UNESCAPED_UNICODE), json: true);
    }
}