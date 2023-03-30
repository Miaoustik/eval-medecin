<?php

namespace App\Controller\Admin\GererAllergen;

use App\Entity\Allergen;
use App\Form\AllergenType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModifyAllergenController extends AbstractController
{
    #[Route(path: '/{id}', name: 'admin_modifyAllergen_index')]
    public function index (Allergen $allergen, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(type: AllergenType::class, options: [
            'action' => $this->generateUrl('admin_modifyAllergen_index', [
                'id' => $allergen->getId()
            ]),
            'method' => 'POST',
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
                    return $this->redirectToRoute('admin_modifyAllergen_index', ['id' => $allergen->getId()]);
                }
            }
            return $this->redirectToRoute('admin_gererAllergen_index');
        }



        return $this->render('admin/gererAllergen/modify.html.twig', [
            'allergen' => $allergen,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/delete/{id}', name: 'admin_modifyAllergen_delete', methods: ['POST'])]
    public function modify (Allergen $diet, EntityManagerInterface $manager, Request $request): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('deleteAllergen', $token)) {
            return new Response(status: 404);
        }

        try {
            $manager->remove($diet);
            $manager->flush();
            $this->addFlash('success', "L'allergie a bien été supprimée.");

        } catch (\Exception $e) {
            $this->addFlash('error', "Il y a eu un problème avec la suppression.");
        }
        return $this->redirectToRoute('admin_gererAllergen_index');
    }
}