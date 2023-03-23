<?php

namespace App\Controller\Admin;

use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/creer-recette')]
#[IsGranted('ROLE_ADMIN')]
class CreateRecipeController extends AbstractAdminController
{
    #[Route(path: '', name: 'admin_createRecipe_index')]
    public function index ( DietRepository $dietRepository, AllergenRepository $allergenRepository): Response
    {
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();


        return $this->render('admin/createRecipe/index.html.twig', [
            'diets' => json_encode($diets, JSON_UNESCAPED_UNICODE),
            'allergens' => json_encode($allergens, JSON_UNESCAPED_UNICODE),
        ]);
    }
}