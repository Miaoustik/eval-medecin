<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/modifier-recette')]
#[IsGranted('ROLE_ADMIN')]
class ModifyRecipeController extends AbstractController
{
    #[Route(path: '/{id}', name: 'admin_modifyRecipe_index')]
    public function index (int $id, DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        //TODO page gerer recette (modifier, supprimer) with ID
        // MODIFIER PAR API
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();

        return $this->render('/admin/modifyRecipe/index.html.twig', [
            "recipeId" => $id,
            "diets" => json_encode($diets, JSON_UNESCAPED_UNICODE),
            "allergens" => json_encode($allergens, JSON_UNESCAPED_UNICODE)
        ]);
    }


}