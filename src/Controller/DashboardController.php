<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {

        return $this->render('backoffice/dashboard/dashboard.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
