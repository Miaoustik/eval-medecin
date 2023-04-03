<?php

namespace App\Controller\Admin\GererRecette;

use App\Entity\Ingredient;
use App\Entity\IngredientRecipe;
use App\Entity\Recipe;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Traits\PaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class GererRecipeController extends AbstractController
{
    use PaginateTrait;

    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    #[Route(path: '/gerer-les-recettes', name: 'admin_gererRecipe_index')]
    public function index (RecipeRepository $repository, Request $request): Response
    {

       $pagination = $this->paginate($request, $repository, 'admin_gererRecipe_index', null, null, ['title', 'id']);

        return $this->render('/admin/gererRecipe/index.html.twig', [
            ...$pagination
        ]);
    }

    #[Route(path: '/modifier-recette/{id}/data')]
    public function modifyApiGet(Recipe $recipe, DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();

        $data = $this->serializer->serialize(data: [$diets, $allergens, $recipe], format: JsonEncoder::FORMAT, context: [
            'groups' => ['MODIFY_RECIPE']
        ]);

        return new JsonResponse(data: $data, headers: [
            'Content-Type' => 'application/json'
        ], json: true);
    }

    #[Route(path: '/creer-recette', name: 'admin_gererRecipe_create')]
    public function create (): Response
    {
        return $this->render('/admin/gererRecipe/create.html.twig');
    }

    #[Route(path: '/creer-recette/data')]
    public function createApiGet (DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();

        $data = $this->serializer->serialize(data: [$diets, $allergens], format: JsonEncoder::FORMAT, context: [
            'groups' => ['MODIFY_RECIPE']
        ]);



        return new JsonResponse(data: $data, headers: [
            'Content-Type' => 'application/json'
        ], json: true);
    }

    #[Route(path: '/creer-recette/create')]
    public function createApiPost (
        Request $request,
        AllergenRepository $allergenRepository,
        DietRepository $dietRepository,
        EntityManagerInterface $manager,
        IngredientRepository $ingredientRepository
    ): Response
    {
        $postRecipe = json_decode($request->getContent());

        $recipe = (new Recipe())
            ->setTitle($postRecipe->title)
            ->setDescription($postRecipe->description)
            ->setBreakTime($postRecipe->repos)
            ->setPreparationTime($postRecipe->preparation)
            ->setCookingTime($postRecipe->cuisson);

        $allergens = $allergenRepository->findAllByNames($postRecipe->allergens);
        $diets = $dietRepository->findAllByNames($postRecipe->diets);

        foreach ($allergens as $allergen) {
            $recipe->addAllergen($allergen);
        }

        foreach ($diets as $diet) {
            $recipe->addDiet($diet);
        }

        $stages = array_filter($postRecipe->stages, function ($e) {
            return trim($e) !== '';
        });
        $recipe->setStages($stages);

        $postIngredients = $postRecipe->ingredients;
        $postIngredientsNames = array_map(function ($e) {
            return $e->name;
        }, $postIngredients);
        $ingredientsWithId = $ingredientRepository->findAllByNames($postIngredientsNames);

        foreach ($postIngredients as $postIngredient) {
            foreach ($ingredientsWithId as $ingredientWithId) {
                if ($ingredientWithId->getName() === $postIngredient->name) {
                    $postIngredient->name = $ingredientWithId;
                }
            }

            $ingredientRecipe = (new IngredientRecipe())
                ->setQuantity($postIngredient->quantity);
            if ($postIngredient->name instanceof Ingredient) {
                $ingredientRecipe->setIngredient($postIngredient->name);
            } else {
                $newIngredient = (new Ingredient())
                    ->setName($postIngredient->name);
                $ingredientRecipe->setIngredient($newIngredient);
            }
            $recipe->addIngredientRecipe($ingredientRecipe);

        }

        $manager->persist($recipe);

        try {
            $manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }

        return new JsonResponse($recipe->getId());
    }

}