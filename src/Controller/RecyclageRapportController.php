<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
final class RecyclageRapportController extends AbstractController
{  private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $e)
    {
        $this->em = $e;
    }
    #[Route('/recyclage/rapport', name: 'app_recyclage_rapport')]
    public function index(): Response
    {
        return $this->render('recyclage_rapport/index.html.twig', [
            'controller_name' => 'RecyclageRapportController',
        ]);
    }
}
