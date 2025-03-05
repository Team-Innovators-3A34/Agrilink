<?php

namespace App\Twig;

use App\Entity\Notification;
use App\Entity\User;
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
            new TwigFunction('getApprovedUsers', [$this, 'getApprovedUsers']),
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

    /*public function getApprovedUsers(): array
    {
        return $this->entityManager->getRepository(User::class)->findBy(
            ['status' => 'approved', 'accountVerification' => 'approved', 'roles' => ['["ROLE_AGRICULTURE","ROLE_USER"]', '["ROLE_RESOURCE_INVESTOR","ROLE_USER"]', '["ROLE_RECYCLING_INVESTOR","ROLE_USER"]']]
        );
    }*/


    public function getApprovedUsers(): array
    {
        $currentUser = $this->security->getUser();

        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->andWhere('u.accountVerification = :accountVerification')
            ->andWhere('u.roles IN (:roles)')
            ->andWhere('u.id != :currentUser') // Exclude the currently logged-in user
            ->setParameter('accountVerification', 'approved')
            ->setParameter('roles', [
                '["ROLE_AGRICULTURE","ROLE_USER"]',
                '["ROLE_RESOURCE_INVESTOR","ROLE_USER"]',
                '["ROLE_RECYCLING_INVESTOR","ROLE_USER"]'
            ])
            ->setParameter('currentUser', $currentUser ? $currentUser->getId() : null) // Exclude by ID
            ->getQuery()
            ->getResult();
    }
}
