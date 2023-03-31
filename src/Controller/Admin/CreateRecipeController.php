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
use PhpParser\JsonDecoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/admin/creer-recette')]
#[IsGranted('ROLE_ADMIN')]
class CreateRecipeController extends AbstractAdminController
{

    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    #[Route(path: '', name: 'admin_createRecipe_index')]
    public function index ( ): Response
    {
        return $this->render('admin/createRecipe/index.html.twig');
    }

    #[Route(path: '/api/getdata', methods: ['GET'])]
    public function getData (DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();

        return new JsonResponse(
          data: $this->serializer->serialize([$diets, $allergens], JsonEncoder::FORMAT, [
              'groups' => ['MODIFY_RECIPE']
            ]
          ),
            headers: [
                'Content-Type' => 'application/json'
              ],
            json: true
        );
    }

    #[Route('/api/create')]
    public function ApiModifyRecipe (
        Request $request,
        IngredientRepository $ingredientRepository,
        DietRepository $dietRepository,
        AllergenRepository $allergenRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $data = json_decode($request->getContent());

        $recipe = new Recipe();

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
            if (count($ids) > 0 ) {
                $allergens = $repository->findByIds($ids);
            } else {
                $allergens = [];
            }



            $str = explode('\\', $repository->getClassName());
            $str = end($str);

            $addMethod = 'add' . $str;

            foreach ($allergens as $allergen) {
                $recipe->$addMethod($allergen);
            }

            foreach ($news as $new) {
                if (trim($new->name) !== '') {
                    $newObj = (new ($repository->getClassName()))
                        ->setName(ucfirst($new->name));
                    $recipe->$addMethod($newObj);
                }

            }

        }

        getItems($data->allergens, $allergenRepository, $recipe);
        getItems($data->diets, $dietRepository, $recipe);

        $recipe->setTitle($data->title)
            ->setDescription($data->description)
            ->setPreparationTime($data->preparationTime)
            ->setBreakTime($data->breakTime)
            ->setCookingTime($data->cookingTime);



        $ingredients = array_map(function ($e) {
            return [
                $e->ingredient->name,
                $e->quantity
            ];
        }, $data->ingredientRecipes);


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

        foreach ($ingredients as $ingredient) {
            //dd($ingredient);

            $newIngredientRecipe = new IngredientRecipe();
            if ($ingredient[0] instanceof Ingredient) {
                $newIngredientRecipe->setIngredient($ingredient[0]);
                $newIngredientRecipe->setQuantity($ingredient[1]);
                $recipe->addIngredientRecipe($newIngredientRecipe);
            } else {
                if (trim($ingredient[0]) !== '') {
                    $newIngredient = (new Ingredient())
                        ->setName($ingredient[0]);
                    $newIngredientRecipe->setIngredient($newIngredient);
                    $newIngredientRecipe->setQuantity($ingredient[1]);
                    $recipe->addIngredientRecipe($newIngredientRecipe);
                }

            }


        }



        $filtredStages = array_filter($data->stages, function ($e) {
            return trim($e) !== '';
        });

        $recipe->setStages($filtredStages);

        //dd($recipe);

        try {
            $entityManager->persist($recipe);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(data: json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE), status: 404 , json: true);
        }

        //dd($recipe);

        return new JsonResponse(data: $recipe->getId(), status: 200);
    }
}