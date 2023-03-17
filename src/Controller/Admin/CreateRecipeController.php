<?php

namespace App\Controller\Admin;

use ApiPlatform\Serializer\JsonEncoder;
use App\Repository\IngredientRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/creer-recette')]
#[IsGranted('ROLE_ADMIN')]
class CreateRecipeController extends AbstractAdminController
{
    #[Route(path: '', name: 'admin_createRecipe_index')]
    public function index (IngredientRepository $ingredientRepository, ): Response
    {
        $ingredients = $ingredientRepository->findAll();




        return $this->render('admin/createRecipe/index.html.twig', [
            'ingredients' => json_encode($ingredients)
        ]);
    }
}