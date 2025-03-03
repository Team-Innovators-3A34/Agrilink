<?php

namespace App\Controller\GestionNotification;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification; // Ensure this is correct
use App\Entity\User; // Ensure this is correct


class NotificationController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/notification/{id}', name: 'app_read_notification')]
    public function readNotifications(String $id): Response
    {
        $notification = $this->entityManager->getRepository(Notification::class)->find($id);
        $notification->setIsRead(true);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        if ($notification->getToUserNotif() == null) {
            if ($notification->getTypeNotification() == "Claim") {
                return $this->redirectToRoute('app_reclamation_list_dashboard');
            } elseif ($notification->getTypeNotification() == "Registration") {
                return $this->redirectToRoute('app_dashboard_users');
            }
        } else {
            if ($notification->getTypeNotification() == "Demande Ressource") {
                return $this->redirectToRoute('app_profilee', ['id' => $notification->getToUserNotif()->getId()]);
            } else if ($notification->getTypeNotification() == "Friend Request") {
                return $this->redirectToRoute('app_profilee', ['id' => $notification->getUserNotif()->getId()]);
            } else if ($notification->getTypeNotification() == "Friend Request Accepted") {
                return $this->redirectToRoute('app_profilee', ['id' => $notification->getUserNotif()->getId()]);
            } else if ($notification->getTypeNotification() == "Demande Ressource Accepted") {
                return $this->redirectToRoute('app_profilee', ['id' => $notification->getUserNotif()->getId()]);
            }
        }
        return $this->redirectToRoute('app_homepage');
    }
}
