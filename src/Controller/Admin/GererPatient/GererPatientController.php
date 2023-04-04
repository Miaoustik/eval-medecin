<?php

namespace App\Controller\Admin\GererPatient;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\Diet;
use App\Entity\User;
use App\Form\GererType;
use App\Form\PatientType;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\UserRepository;
use App\Traits\PaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class GererPatientController extends AbstractController
{
    use PaginateTrait;

    #[Route(path: '/gerer-les-patients', name: 'admin_gererPatient_index')]
    public function index (UserRepository $repository, Request $request): Response
    {
        $pagination = $this->paginate($request, $repository, 'admin_gererPatient_index', 10, 'email', criteria: ["roles" => 'ROLE_USER']);

        return $this->render('/admin/gererPatient/index.html.twig', [
            ...$pagination
        ]);
    }

    #[Route(path: '/modifier-un-patient/{id}', name: 'admin_gererPatient_modify')]
    public function modify (
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        DietRepository $dietRepository,
        AllergenRepository $allergenRepository,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->request->all();
            //dd($data);

            if ($data['email'] !== '') {
                $user->setEmail($data['email']);
            }

            if ($data['password'] !== '') {
                $user->setEmail($data['email']);
            }

            if (isset($data['allergens']) && count($data['allergens']) > 0) {
                $newAllergens = $allergenRepository->findByIds($data['allergens']);

                $allergens = $user->getAllergens();

                foreach ($allergens as $allergen) {
                    $user->removeAllergen($allergen);
                }
                foreach ($newAllergens as $newAllergen) {
                    $user->addAllergen($newAllergen);
                }

            }

            if (isset($data['diets']) && count($data['diets']) > 0) {
                $newDiets = $dietRepository->findByIds($data['diets']);

                $diets = $user->getDiets();

                foreach ($diets as $diet) {
                    $user->removeDiet($diet);
                }
                foreach ($newDiets as $newDiet) {
                    $user->addDiet($newDiet);
                }

            }

            try {
                $manager->flush();
                $this->addFlash('success', 'Le patient a bien été modifié');
                return $this->redirectToRoute('admin_gererPatient_index');
            } catch (\Exception $exception) {
                $this->addFlash('error', "La valeur '" . explode("'", $exception->getMessage())[1] . ' existe déjà.');
            }
        }
        $allergens = $allergenRepository->findAll();
        $diets = $dietRepository->findAll();


        return $this->render('admin/gererPatient/modify.html.twig', [
            'user' => $user,
            'allergens' => $allergens,
            'diets' => $diets
        ]);
    }

    #[Route(path: '/delete-user/{id}', name: 'admin_gererPatient_delete')]
    public function delete (User $user, EntityManagerInterface $manager, Request $request): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return new Response(status: 404);
        }

        try {
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', "Le patient a bien été supprimé.");

        } catch (\Exception $e) {
            $this->addFlash('error', "Il y a eu un problème avec la suppression.");
        }
        return $this->redirectToRoute('admin_gererPatient_index');
    }

    #[Route(path: '/creer-un-patient', name: 'admin_gererPatient_create')]
    public function create (
        Request $request,
        EntityManagerInterface $manager,
        DietRepository $dietRepository,
        AllergenRepository $allergenRepository,
        UserPasswordHasherInterface $hasher
    ): Response
    {

        if ($request->getMethod() === 'POST') {
            $data = $request->request->all();
            $user = new User();
            //dd($data);

            if ($data['email'] !== '') {
                $user->setEmail($data['email']);
            }

            if ($data['password'] !== '') {
                $user->setPassword($hasher->hashPassword($user, $data['password']));
            }

            if (count($data['allergens']) > 0) {
                $newAllergens = $allergenRepository->findByIds($data['allergens']);

                foreach ($newAllergens as $newAllergen) {
                    $user->addAllergen($newAllergen);
                }

            }

            if (count($data['diets']) > 0) {
                $newDiets = $dietRepository->findByIds($data['diets']);

                foreach ($newDiets as $newDiet) {
                    $user->addDiet($newDiet);
                }

            }

            try {
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Le patient a bien été crée');
                return $this->redirectToRoute('admin_gererPatient_index');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
                dd($exception);
            }
        }
        $diets = $dietRepository->findAll();
        $allergens = $allergenRepository->findAll();


        return $this->render('admin/gererPatient/modify.html.twig', [
            'diets' => $diets,
            'allergens' => $allergens
        ]);
    }
}