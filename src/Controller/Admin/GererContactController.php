<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Traits\PaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class GererContactController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '/gerer-les-messages', name: 'admin_gererContact_index')]
    public function index(Request $request, ContactRepository $repository): Response
    {
        $pagination = $this->paginate(
            request: $request,
            repository: $repository,
            property: ['id', 'email']
        );
        return $this->render('/admin/contact/index.html.twig', [
            ...$pagination
        ]);
    }

    #[Route(path: '/gerer-les-messages/remove/{id}', name: 'admin_gererContact_delete')]
    public function remove(Contact $contact, Request $request, EntityManagerInterface $manager ): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return new Response(status: 404);
        }

        try {
            $manager->remove($contact);
            $manager->flush();
            $this->addFlash('success', "Le message a bien été supprimé.");

        } catch (\Exception $e) {
            $this->addFlash('error', "Il y a eu un problème avec la suppression.");
        }
        return $this->redirectToRoute('admin_gererContact_index');
    }

    #[Route(path: '/gerer-les-messages/{id}', name: 'admin_gererContact_show')]
    public function show(Contact $contact): Response
    {
        return $this->render('admin/contact/show.html.twig', [
            'contact' => $contact
        ]);
    }
}