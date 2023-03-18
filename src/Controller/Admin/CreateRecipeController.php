<?php

namespace App\Controller\Admin;

use ApiPlatform\Serializer\JsonEncoder;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\IngredientRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/creer-recette')]
#[IsGranted('ROLE_ADMIN')]
class CreateRecipeController extends AbstractAdminController
{
    #[Route(path: '', name: 'admin_createRecipe_index')]
    public function index (IngredientRepository $ingredientRepository, DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        $ingredients = $ingredientRepository->findAll();
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();


        return $this->render('admin/createRecipe/index.html.twig', [
            'ingredients' => json_encode($ingredients),
            'diets' => json_encode($diets),
            'allergens' => json_encode($allergens),
        ]);
    }
}