<?php

namespace App\Controller\Admin;

use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use PhpParser\JsonDecoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}