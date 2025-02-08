<?php

namespace App\Twig;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GlobalNotificationsExtension extends AbstractExtension
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getNotificationsBackoffice', [$this, 'getNotificationsBackoffice']),
        ];
    }

    public function getNotificationsBackoffice(): array
    {
        $user = $this->security->getUser();

        if (!$user) {
            return [];
        }

        return $this->entityManager->getRepository(Notification::class)->findBy(
            ['isRead' => false, 'toUserNotif' => null],
            ['createdAt' => 'DESC']
        );
    }
}
