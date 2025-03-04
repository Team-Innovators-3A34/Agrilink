<?php

namespace App\Service;

use App\Entity\Demandes;
use App\Entity\Friendship;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService
{
    private $hub;
    private $entityManager;

    private HttpClientInterface $httpClient;


    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->hub = $hub;
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
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

        $response = $this->httpClient->request('POST', "http://localhost:3000/.well-known/mercure", [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.KKPIikwUzRuB3DTpVw6ajzwSChwFw5omBMmMcWKiDcM',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query([
                'topic' => 'https://example.com/books/1',
                'data' => json_encode([
                    'status' => $notification->getMessage(),
                    'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                    'username' => $user->getNom() . ' ' . $user->getPrenom(),
                    'image' => $user->getImage(),
                    'id' => $notification->getId()
                ])
            ])
        ]);
    }

    public function claimNotification(User $user): void
    {
        $notification = new Notification();
        $notification->setMessage("New Claim available");
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setUserNotif($user);
        $notification->setTypeNotification("Claim");
        $notification->setToUserNotif(null);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $response = $this->httpClient->request('POST', "http://localhost:3000/.well-known/mercure", [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.KKPIikwUzRuB3DTpVw6ajzwSChwFw5omBMmMcWKiDcM',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query([
                'topic' => 'https://example.com/books/1',
                'data' => json_encode([
                    'status' => $notification->getMessage(),
                    'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                    'username' => $user->getNom() . ' ' . $user->getPrenom(),
                    'image' => $user->getImage(),
                    'id' => $notification->getId()
                ])
            ])
        ]);
    }


    public function demandeNotification(User $user, User $usercon): void
    {
        // Create and save the notification
        $notification = new Notification();
        $notification->setIsRead(false);
        $notification->setMessage('Vous avez une nouvelle demande pour votre ressource');
        $notification->setTypeNotification('Demande Ressource');
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setToUserNotif($user);

        $notification->setUserNotif($usercon);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $response = $this->httpClient->request('POST', "http://localhost:3000/.well-known/mercure", [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.KKPIikwUzRuB3DTpVw6ajzwSChwFw5omBMmMcWKiDcM',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query([
                'topic' => 'https://example.com/books/1',
                'data' => json_encode([
                    'status' => $notification->getMessage(),
                    'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                    'username' => $user->getNom() . ' ' . $user->getPrenom(),
                    'image' => $user->getImage(),
                    'id' => $notification->getId()
                ])
            ])
        ]);
    }

    public function friendRequestNotification(User $user, User $usercon): void
    {
        // Create and save the notification
        $notification = new Notification();
        $notification->setIsRead(false);
        $notification->setMessage('Vous avez une demande d\'amitié');
        $notification->setTypeNotification('Friend Request');
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setToUserNotif($user);
        $notification->setUserNotif($usercon);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $response = $this->httpClient->request('POST', "http://localhost:3000/.well-known/mercure", [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.KKPIikwUzRuB3DTpVw6ajzwSChwFw5omBMmMcWKiDcM',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query([
                'topic' => 'https://example.com/notificationfront/' . $user->getId(),
                'data' => json_encode([
                    'status' => $notification->getMessage(),
                    'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                    'username' => $usercon->getNom() . ' ' . $usercon->getPrenom(),
                    'image' => $usercon->getImage(),
                    'id' => $notification->getId()
                ])
            ])
        ]);
    }

    public function acceptFriendRequestNotification(Friendship $friendship): void
    {
        $notification = new Notification();
        $notification->setMessage('Votre demande d\'amitié a été acceptée');
        $notification->setUserNotif($friendship->getFriend());
        $notification->setToUserNotif($friendship->getUser());
        $notification->setTypeNotification('Friend Request Accepted');
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $response = $this->httpClient->request('POST', "http://localhost:3000/.well-known/mercure", [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.KKPIikwUzRuB3DTpVw6ajzwSChwFw5omBMmMcWKiDcM',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query([
                'topic' => 'https://example.com/notificationfront/' . $friendship->getUser()->getId(),
                'data' => json_encode([
                    'status' => $notification->getMessage(),
                    'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                    'username' => $friendship->getFriend()->getNom() . ' ' . $friendship->getFriend()->getPrenom(),
                    'image' => $friendship->getFriend()->getImage(),
                    'id' => $notification->getId()
                ])
            ])
        ]);
    }

    public function acceptDemandeRessourceNotification(Demandes $demande): void
    {
        $notification = new Notification();
        $notification->setMessage('Votre demande de ressource de ' . $demande->getRessourceId()->getNameR() . ' a été acceptée');
        $notification->setUserNotif($demande->getToUser());
        $notification->setToUserNotif($demande->getUserID());
        $notification->setTypeNotification('Demande Ressource Accepted');
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $response = $this->httpClient->request('POST', "http://localhost:3000/.well-known/mercure", [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.KKPIikwUzRuB3DTpVw6ajzwSChwFw5omBMmMcWKiDcM',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query([
                'topic' => 'https://example.com/notificationfront/' . $demande->getUserID()->getId(),
                'data' => json_encode([
                    'status' => $notification->getMessage(),
                    'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                    'username' => $demande->getToUser()->getNom() . ' ' . $demande->getToUser()->getPrenom(),
                    'image' => $demande->getToUser()->getImage(),
                    'id' => $notification->getId()
                ])
            ])
        ]);
    }
}
