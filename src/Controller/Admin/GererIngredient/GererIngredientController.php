<?php

namespace App\Controller\Admin\GererIngredient;

use App\Entity\Ingredient;
use App\Form\GererType;
use App\Repository\IngredientRepository;
use App\Traits\PaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GererIngredientController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '/gerer-les-ingredients', name: 'admin_gererIngredient_index')]
    public function index (IngredientRepository $repository, Request $request): Response
    {
        $pagination = $this->paginate($request, $repository, 'admin_gererIngredient_index', 10, 'name');

        return $this->render('/admin/gererIngredient/index.html.twig', [
            ...$pagination
        ]);
    }

    #[Route(path: '/modifier-un-ingredient/{id}', name: 'admin_gererIngredient_modify')]
    public function modify (Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(type: GererType::class, data: new Ingredient(), options: [
            "label" => "Nouveau nom de l'ingredient : ",
            'btnTxt' => 'Modifier',
            'method' => 'POST',
            'data_class' => Ingredient::class,
            'action' => $this->generateUrl('admin_gererIngredient_modify', [
                'id' => $ingredient->getId()
            ]),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Ingredient $formIngredient */
            $formIngredient = $form->getData();

            $name = $formIngredient->getName();

            try {
                $ingredient->setName($name);
                $manager->flush();
                $this->addFlash('success', "l'ingredient a bien été modifiée");

            } catch (\Exception $e) {
                if ($e->getCode() === 1062) {
                    $this->addFlash('error', "l'ingredient' '$name' éxiste déjà.");
                    return $this->redirectToRoute('admin_gererIngredient_modify', ['id' => $ingredient->getId()]);
                }
            }
            return $this->redirectToRoute('admin_gererIngredient_index');
        }



        return $this->render('admin/gererIngredient/modify.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/delete-Ingredient/{id}', name: 'admin_gererIngredient_delete', methods: ['POST'])]
    public function delete (Ingredient $ingredient, EntityManagerInterface $manager, Request $request): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return new Response(status: 404);
        }

        try {
            $manager->remove($ingredient);
            $manager->flush();
            $this->addFlash('success', "l'ingredient a bien été supprimée.");

        } catch (\Exception $e) {
            //dd($e);
            $this->addFlash('error', "Il y a eu un problème avec la suppression.");
        }
        return $this->redirectToRoute('admin_gererIngredient_index');
    }

    #[Route(path: '/creer-un-ingredient', name: 'admin_gererIngredient_create')]
    public function create (Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(type: GererType::class, data: new Ingredient(), options: [
            "label" => 'Nom de la nouvelle allergie',
            'btnTxt' => 'Créer',
            'method' => 'POST',
            'data_class' => Ingredient::class,
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Ingredient $formDiet */
            $ingredient = $form->getData();

            try {
                $manager->persist($ingredient);
                $manager->flush();
                $this->addFlash('success', "l'ingredient a bien été crée");

            } catch (\Exception $e) {
                if ($e->getCode() === 1062) {
                    $this->addFlash('error', "l'ingredient '" . $ingredient->getName() . "' éxiste déjà.");
                    return $this->redirectToRoute('admin_gererIngredient_create');
                }
            }
            return $this->redirectToRoute('admin_gererIngredient_index');
        }



        return $this->render('admin/gererIngredient/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}