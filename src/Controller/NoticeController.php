<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Repository\NoticeRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class NoticeController extends AbstractController
{

    public function __construct(private readonly SerializerInterface $serializer, private readonly CsrfTokenManagerInterface $tokenManager)
    {
    }

    #[Route(path: '/api/recette/{id}/avis', methods: ['GET'])]
    public function getNotices ($id, NoticeRepository $repository): Response
    {
        $avis = $repository->findWithRecipeId($id);
        $user = $this->getUser();

        $alreadyNoticed = false;

        foreach ($avis as $avi) {
            if ($avi->getUser()->getId() === $user->getId()) {
                $alreadyNoticed = true;
            }
        }

        $avisJson = $this->serializer->serialize([$avis, $alreadyNoticed], JsonEncoder::FORMAT, [
            'groups' => ['READ_NOTICE']
        ]);

        return new JsonResponse(data: $avisJson, json: true);
    }

    #[Route(path: '/api/avis/creer', methods: ['POST'])]
    public function addNotice (Request $request, RecipeRepository $recipeRepository, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $data = json_decode($request->getContent());
        $recipe = $recipeRepository->find($data->recipeid);

        $notice = (new Notice())
            ->setContent($data->content)
            ->setNote($data->note)
            ->setUser($this->getUser());

        $recipe->addNotice($notice);

        try {
            $manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(status: 404);
        }

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}