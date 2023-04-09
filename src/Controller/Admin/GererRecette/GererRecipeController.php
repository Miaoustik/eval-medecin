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

       $pagination = $this->paginate(
           request: $request,
           repository: $repository,
           property: ['title', 'id']
       );

        return $this->render('/admin/gererRecipe/index.html.twig', [
            ...$pagination
        ]);
    }

    #[Route(path: '/modifier-recette/{id}', name: 'admin_gererRecipe_modify')]
    public function modify ($id)
    {
        return $this->render('/admin/gererRecipe/modify.html.twig', [
            'recipeid' => $id
        ]);
    }

    #[Route(path: '/api/modifier-recette/{id}/modify', methods: ['POST'])]
    public function modifyApiPost (
        Request $request,
        AllergenRepository $allergenRepository,
        DietRepository $dietRepository,
        IngredientRepository $ingredientRepository,
        Recipe $recipe,
        EntityManagerInterface $manager
    ): Response
    {
        $postRecipe = json_decode($request->getContent());
        $recipe = $this->setRecipe($postRecipe, $allergenRepository, $dietRepository, $ingredientRepository, $recipe, true);


        try {
            $manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }

        return new JsonResponse($recipe->getId());
    }

    #[Route(path: '/api/modifier-recette/{id}/data', methods: ['GET'])]
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

    #[Route(path: '/api/creer-recette/data', methods: ['GET'])]
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

    #[Route(path: '/api/creer-recette/create', methods: ['POST'])]
    public function createApiPost (
        Request $request,
        AllergenRepository $allergenRepository,
        DietRepository $dietRepository,
        EntityManagerInterface $manager,
        IngredientRepository $ingredientRepository
    ): Response
    {
        $postRecipe = json_decode($request->getContent());
        $recipe = $this->setRecipe($postRecipe, $allergenRepository, $dietRepository, $ingredientRepository, new Recipe());

        $manager->persist($recipe);

        try {
            $manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }

        return new JsonResponse($recipe->getId());
    }

    #[Route(path: '/supprimer-recette/{id}', name: 'admin_gererRecipe_delete', methods: ['POST'])]
    public function delete (Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return new Response(status: 404);
        }

        try {
            $manager->remove($recipe);
            $manager->flush();
            $this->addFlash('success', "La recette a bien été supprimée.");

        } catch (\Exception $e) {
            $this->addFlash('error', "Il y a eu un problème avec la suppression.");
        }
        return $this->redirectToRoute('admin_gererRecipe_index');
    }

    private function setRecipe($postRecipe,AllergenRepository $allergenRepository, DietRepository $dietRepository, IngredientRepository $ingredientRepository, Recipe $recipe, bool $modify = false): Recipe
    {
        $recipe
            ->setTitle($postRecipe->title)
            ->setDescription($postRecipe->description)
            ->setBreakTime($postRecipe->repos)
            ->setPreparationTime($postRecipe->preparation)
            ->setCookingTime($postRecipe->cuisson)
            ->setPatientOnly($postRecipe->patientOnly);

        $allergens = $allergenRepository->findAllByNames($postRecipe->allergens);
        $diets = $dietRepository->findAllByNames($postRecipe->diets);

        if ($modify) {
            foreach ($recipe->getAllergens() as $allergen) {
                $recipe->removeAllergen($allergen);
            }
            foreach ($recipe->getDiets() as $diet) {
                $recipe->removeDiet($diet);
            }
        }

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

        foreach ($postIngredients as $index => $postIngredient) {
            foreach ($ingredientsWithId as $ingredientWithId) {
                if ($ingredientWithId->getName() === $postIngredient->name) {
                    $postIngredient->name = $ingredientWithId;
                }
            }
            $oldingredientRecipe = $recipe->getIngredientRecipes();

            if (isset($oldingredientRecipe[$index])) {
                $ingredientRecipe = $oldingredientRecipe[$index];
                $ingredientRecipe->setQuantity($postIngredient->quantity);
            } else {
                $ingredientRecipe = (new IngredientRecipe())
                    ->setQuantity($postIngredient->quantity);
            }



            if ($postIngredient->name instanceof Ingredient) {
                $ingredientRecipe->setIngredient($postIngredient->name);
            } else {
                $newIngredient = (new Ingredient())
                    ->setName($postIngredient->name);
                $ingredientRecipe->setIngredient($newIngredient);
            }

            if (!$ingredientRecipe->getId()) {
                $recipe->addIngredientRecipe($ingredientRecipe);
            }

        }
        return $recipe;
    }

}