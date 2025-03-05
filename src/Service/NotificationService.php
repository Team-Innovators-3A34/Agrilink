<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    private $hub;
    private $entityManager;

    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager)
    {
        $this->hub = $hub;
        $this->entityManager = $entityManager;
    }

    public function registerNotification(User $user): void
    {
        // Create and save the notification
        $notification = new Notification();
        $notification->setMessage('New Registration available');
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable('now'));
        $notification->setUserNotif($user);
        $notification->setTypeNotification('Registration');
        $notification->setToUserNotif(null);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        // Publish the notification
        $update = new Update(
            'https://example.com/books/1',  // Topic identifier (you can make this dynamic if needed)
            json_encode([
                'status' => $notification->getMessage(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                'username' => $user->getNom() . ' ' . $user->getPrenom(),
                'image' => $user->getImage(),
                'id' => $notification->getId()
            ])
        );

        $this->hub->publish($update);
    }

    public function claimNotification(User $user): void
    {
        // Create and save the notification
        $notification = new Notification();
        $notification->setMessage("New Claim available");
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable('now'));
        $notification->setUserNotif($user);
        $notification->setTypeNotification("Claim");
        $notification->setToUserNotif(null);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        // Publish the notification
        $update = new Update(
            'https://example.com/books/1',  // Topic identifier (you can make this dynamic if needed)
            json_encode([
                'status' => $notification->getMessage(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                'username' => $user->getNom() . ' ' . $user->getPrenom(),
                'image' => $user->getImage(),
                'id' => $notification->getId(),
                'type' => $notification->getTypeNotification()
            ])
        );

        $this->hub->publish($update);
    }

}
