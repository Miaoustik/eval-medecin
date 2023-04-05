<?php

namespace App\Controller\Admin\GererDiet;

use App\Entity\Diet;
use App\Form\GererType;
use App\Repository\DietRepository;
use App\Traits\PaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class GererDietController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '/gerer-les-regimes', name: 'admin_gererDiet_index')]
    public function index (DietRepository $repository, Request $request): Response
    {
        $pagination = $this->paginate(
            request: $request,
            repository: $repository,
            property: ['id', 'name']
        );
        return $this->render('/admin/gererDiet/index.html.twig', [
            ...$pagination
        ]);
    }

    #[Route(path: '/modifier-un-regime/{id}', name: 'admin_gererDiet_modify')]
    public function modify (Diet $diet, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(type: GererType::class, options: [
            'action' => $this->generateUrl('admin_gererDiet_modify', [
                'id' => $diet->getId()
            ]),
            'method' => 'POST',
            "label" => 'Nouveau nom du régime : ',
            'btnTxt' => 'Créer',
            'data_class' => Diet::class,
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

    #[Route(path: '/delete-diet/{id}', name: 'admin_gererDiet_delete', methods: ['POST'])]
    public function delete (Diet $diet, EntityManagerInterface $manager, Request $request): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
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

    #[Route(path: '/creer-un-regime', name: 'admin_gererDiet_create')]
    public function create (Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(type: GererType::class, data: new Diet(), options: [
            "label" => 'Nom du nouveau régime',
            'btnTxt' => 'Créer',
            'method' => 'POST',
            'data_class' => Diet::class,
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Diet $formDiet */
            $diet = $form->getData();

            try {
                $manager->persist($diet);
                $manager->flush();
                $this->addFlash('success', 'Le régime a bien été crée');

            } catch (\Exception $e) {
                if ($e->getCode() === 1062) {
                    $this->addFlash('error', "Le régime '" . $diet->getName() . "' éxiste déjà.");
                    return $this->redirectToRoute('admin_gererDiet_index', ['id' => $diet->getId()]);
                }
            }
            return $this->redirectToRoute('admin_gererDiet_index');
        }



        return $this->render('admin/gererDiet/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


}