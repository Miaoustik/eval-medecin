<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Traits\PaginateTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recettes')]
class RecipesController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '', name: 'recipes_index')]
    public function index (RecipeRepository $repository, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $criteria = [];

        if (!$user) {
            $criteria['patientOnly'] = false;
        } else {
            $criteria['collection'] = ['diets' => $user->getDiets()];
            $criteria['collection']['allergens'] = $user->getAllergens();
        }

        $paginationParams = $this->paginateWithSearch(
            request: $request,
            repository: $repository,
            searchBy: 'title',
            order: 'title',
            property: ['title', 'id'],
            criteria: $criteria
        );


        return $this->render('recipes/index.html.twig', [
            ...$paginationParams
        ]);
    }
}