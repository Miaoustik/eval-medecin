<?php

namespace App\Controller\Admin\GererAllergen;

use App\Entity\Allergen;
use App\Form\GererType;
use App\Repository\AllergenRepository;
use App\Traits\PaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class GererAllergenController extends AbstractController
{

    use PaginateTrait;

    #[Route(path: '/gerer-les-allergies', name: 'admin_gererAllergen_index')]
    public function index (AllergenRepository $repository, Request $request): Response
    {
        $pagination = $this->paginate($request, $repository, 'admin_gererAllergen_index', 10, 'name');

        return $this->render('/admin/gererAllergen/index.html.twig', [
            ...$pagination
        ]);
    }

    #[Route(path: '/modifier-une-allergie/{id}', name: 'admin_gererAllergen_modify')]
    public function modify (Allergen $allergen, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(type: GererType::class, data: new Allergen(), options: [
            "label" => "Nouveau nom de l'allergie : ",
            'btnTxt' => 'Modifier',
            'method' => 'POST',
            'data_class' => Allergen::class,
            'action' => $this->generateUrl('admin_gererAllergen_modify', [
                'id' => $allergen->getId()
            ]),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Allergen $formAllergen */
            $formAllergen = $form->getData();

            $name = $formAllergen->getName();

            try {
                $allergen->setName($name);
                $manager->flush();
                $this->addFlash('success', "L'allergie a bien été modifiée");

            } catch (\Exception $e) {
                if ($e->getCode() === 1062) {
                    $this->addFlash('error', "L'allergie' '$name' éxiste déjà.");
                    return $this->redirectToRoute('admin_gererAllergen_modify', ['id' => $allergen->getId()]);
                }
            }
            return $this->redirectToRoute('admin_gererAllergen_index');
        }



        return $this->render('admin/gererAllergen/modify.html.twig', [
            'allergen' => $allergen,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/delete-allergen/{id}', name: 'admin_gererAllergen_delete', methods: ['POST'])]
    public function delete (Allergen $allergen, EntityManagerInterface $manager, Request $request): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return new Response(status: 404);
        }

        try {
            $manager->remove($allergen);
            $manager->flush();
            $this->addFlash('success', "L'allergie a bien été supprimée.");

        } catch (\Exception $e) {
            $this->addFlash('error', "Il y a eu un problème avec la suppression.");
        }
        return $this->redirectToRoute('admin_gererAllergen_index');
    }

    #[Route(path: '/creer-une-allergie', name: 'admin_gererAllergen_create')]
    public function create (Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(type: GererType::class, data: new Allergen(), options: [
            "label" => 'Nom de la nouvelle allergie',
            'btnTxt' => 'Créer',
            'method' => 'POST',
            'data_class' => Allergen::class,
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Allergen $formDiet */
            $allergen = $form->getData();

            try {
                $manager->persist($allergen);
                $manager->flush();
                $this->addFlash('success', "L'allergie a bien été crée");

            } catch (\Exception $e) {
                if ($e->getCode() === 1062) {
                    $this->addFlash('error', "L'allergie '" . $allergen->getName() . "' éxiste déjà.");
                    return $this->redirectToRoute('admin_gererAllergen_create');
                }
            }
            return $this->redirectToRoute('admin_gererAllergen_index');
        }



        return $this->render('admin/gererAllergen/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}