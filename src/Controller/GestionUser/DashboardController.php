<?php

namespace App\Controller\GestionUser;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

final class DashboardController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {

        $notifications = $this->entityManager->getRepository(Notification::class)->findBy(['isRead' => false], ['createdAt' => 'DESC']);

        return $this->render('backoffice/dashboard/dashboard.html.twig', [
            'notifications' => $notifications
        ]);
    }
}
