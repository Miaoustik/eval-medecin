<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/contact')]
class ContactController extends AbstractController
{
    #[Route(path: '', name: 'contact_index')]
    public function index (Request $request, EntityManagerInterface $manager): Response
    {

        if ($request->getMethod() === 'POST') {
            if (!$this->isCsrfTokenValid('contact', $request->request->get('_token')) || !empty($request->request->get('name'))) {
                return new Response(status: 404);
            }

            /** @var User $user */
            $user = $this->getUser();

            $email = $request->request->get('email');


            $contact = (new Contact())
                ->setMessage($request->request->get('message'));

            if ($user) {
                $contact->setEmail($user->getEmail());
            } else {
                $contact->setEmail($email);
            }

            try {
                $manager->persist($contact);
                $manager->flush();
                $this->addFlash('success', "Votre message a bien été envoyé.");
                return $this->redirectToRoute('home_index');
            } catch (\Exception $error) {
                $this->addFlash('error', 'Problème avec la création de message.');
            }
        }

        return $this->render('contact/index.html.twig');
    }
}