<?php

namespace App\Twig;

use App\Entity\Friendship;
use App\Entity\Notification;
use App\Entity\User;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GlobalNotificationsExtension extends AbstractExtension
{
    private $entityManager;
    private $security;
    private $httpClient;

    public function __construct(EntityManagerInterface $entityManager, Security $security, HttpClientInterface $httpClient)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->httpClient = $httpClient;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getNotificationsBackoffice', [$this, 'getNotificationsBackoffice']),
            new TwigFunction('getApprovedUsers', [$this, 'getApprovedUsers']),
            new TwigFunction('getMatchedUsers', [$this, 'getMatchedUsers']), // Added this function
            new TwigFunction('getAllMyFriendRequests', [$this, 'getAllMyFriendRequests']), // Added this function
            new TwigFunction('getAllMyContact', [$this, 'getAllMyContact']), // Added this function
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

    public function getMatchedUsers(): array
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return [];
        }

        $userId = $currentUser->getId();
        $users = $this->entityManager->getRepository(User::class)->getAllUsersForMatching();

        $userData = array_map(fn($user) => [
            'id' => $user->getId(),
            'description' => $user->getDescription(),
            'latitude' => $user->getLatitude(),
            'longitude' => $user->getLongitude(),
            'score' => $user->getScore(),
        ], $users);

        try {
            $response = $this->httpClient->request('POST', 'http://127.0.0.1:5000/match', [
                'json' => ['user_id' => $userId, 'users' => $userData]
            ]);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
        } catch (\Exception $e) {
            return [];
        }

        if ($statusCode !== 200) {
            return [];
        }

        $data = json_decode($content, true);
        if (!isset($data['matches']) || !is_array($data['matches'])) {
            return [];
        }

        return $this->entityManager->getRepository(User::class)->findBy(['id' => $data['matches']]);
    }

    public function getAllMyFriendRequests(): array
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return [];
        }

        return $this->entityManager->getRepository(Friendship::class)->findBy(
            ['friend' => $currentUser->getId(), 'status' => 'pending']
        );
    }

    public function getAllMyContact(): array
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return [];
        }

         return $this->entityManager->getRepository(Friendship::class)->createQueryBuilder('f')
            ->where('f.user = :currentUser OR f.friend = :currentUser')
            ->andWhere('f.status = :status')
            ->setParameter('currentUser', $currentUser->getId())
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getResult();

      
    }
}
