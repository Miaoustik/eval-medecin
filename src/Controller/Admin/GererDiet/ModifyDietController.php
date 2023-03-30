<?php

namespace App\Controller\Admin\GererDiet;

use App\Entity\Diet;
use App\Form\DietType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/modifier-un-regime')]
#[IsGranted('ROLE_ADMIN')]
class ModifyDietController extends AbstractController
{
    #[Route(path: '/{id}', name: 'admin_modifyDiet_index')]
    public function index (Diet $diet, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(type: DietType::class, options: [
            'action' => $this->generateUrl('admin_modifyDiet_index', [
                'id' => $diet->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Diet $formDiet */
            $formDiet = $form->getData();

            $name = $formDiet->getName();

            try {
                $diet->setName($name);
                $manager->flush();
                $this->addFlash('success', 'Le régime a bien été modifié');

            } catch (\Exception $e) {
                if ($e->getCode() === 1062) {
                    $this->addFlash('error', "Le régime '$name' éxiste déjà.");
                    return $this->redirectToRoute('admin_modifyDiet_index', ['id' => $diet->getId()]);
                }
            }
            return $this->redirectToRoute('admin_gererDiet_index');
        }



        return $this->render('admin/gererDiet/modify.html.twig', [
            'diet' => $diet,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/delete/{id}', name: 'admin_modifyDiet_delete', methods: ['POST'])]
    public function modify (Diet $diet, EntityManagerInterface $manager, Request $request): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('deleteDiet', $token)) {
            return new Response(status: 404);
        }

        try {
            $manager->remove($diet);
            $manager->flush();
            $this->addFlash('success', "Le régime a bien été supprimé.");

        } catch (\Exception $e) {
            $this->addFlash('error', "Il y a eu un problème avec la suppression.");
        }
        return $this->redirectToRoute('admin_gererDiet_index');
    }
}