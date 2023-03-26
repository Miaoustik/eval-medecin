<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Entity\IngredientRecipe;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/admin/modifier-recette')]
#[IsGranted('ROLE_ADMIN')]
class ModifyRecipeController extends AbstractController
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    #[Route(path: '/{id}', name: 'admin_modifyRecipe_index')]
    public function index (int $id, ): Response
    {
        return $this->render('/admin/modifyRecipe/index.html.twig', [
            "recipeId" => $id,
        ]);
    }

    #[Route(path: '/api/getdata/{id}', methods: ['GET'])]
    public function ApiGetData($id, RecipeRepository $recipeRepository, DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();

        $recipe = $recipeRepository->findByIdRecipe($id);



        return new JsonResponse(data: $this->serializer->serialize([$recipe, $diets, $allergens], JsonEncoder::FORMAT, [
            'groups' => ['MODIFY_RECIPE']
        ]), json: true);
    }

    #[Route('/api/modify')]
    public function ApiModifyRecipe (
        Request $request,
        RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository,
        DietRepository $dietRepository,
        AllergenRepository $allergenRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $data = json_decode($request->getContent(), true);
        //dd($data);

        $recipe = $recipeRepository->findTest($data['id']);

        function getItems ($data, $repository, $recipe) {

            $ids = [];
            $news = [];
            foreach ($data as $id) {
                if (gettype($id) === 'integer') {
                    $ids[] = $id;
                } else {
                    $news[] = $id;
                }
            }

            $allergens = $repository->findByIds($ids);

            $str = explode('\\', $repository->getClassName());
            $str = end($str);
            $method = 'remove' . $str;
            $getMethod = 'get' . $str . 's';
            foreach ($recipe->$getMethod() as $allergen) {
                $recipe->$method($allergen);
            }

            $addMethod = 'add' . $str;

            foreach ($allergens as $allergen) {
                $recipe->$addMethod($allergen);
            }

            foreach ($news as $new) {
                $newObj = (new ($repository->getClassName()))
                    ->setName(ucfirst($new['name']));
                $recipe->$addMethod($newObj);
            }

        }

        getItems($data['allergens'], $allergenRepository, $recipe);
        getItems($data['diets'], $dietRepository, $recipe);

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
            return new JsonResponse(data: json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE), status: 404 , json: true);
        }

        $recipeJson = $this->serializer->serialize($recipe, JsonEncoder::FORMAT, [
            'groups' => ['MODIFY_RECIPE']
        ]);

        return new JsonResponse(data: $recipeJson, json: true);
    }

}