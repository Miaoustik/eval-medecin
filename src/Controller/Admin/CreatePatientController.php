<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/creer-patient')]
#[IsGranted('ROLE_ADMIN')]
class CreatePatientController extends AbstractAdminController
{
    #[Route(path: '', name: 'admin_createPatient_index')]
    public function index (): Response
    {

        return $this->render('admin/createPatient/index.html.twig');
    }
}