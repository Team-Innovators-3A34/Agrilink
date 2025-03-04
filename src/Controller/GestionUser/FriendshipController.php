<?php

namespace App\Controller\GestionUser;

use App\Entity\Event;
use App\Entity\Friendship;
use App\Entity\Notification;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Posts;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class FriendshipController extends AbstractController
{
    private EntityManagerInterface $em;
    private $notificationservice;

    public function __construct(EntityManagerInterface $em, NotificationService $notificationservice)
    {
        $this->em = $em;
        $this->notificationservice = $notificationservice;
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/send_friend_request/{id}', name: 'app_send_friend_request')]
    public function SendFriendRequest(User $user): Response
    {
        $friendship = new Friendship();
        $friendship->setUser($this->getUser());
        $friendship->setFriend($user);
        $friendship->setStatus('pending');
        $friendship->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($friendship);
        $this->em->flush();

        $this->notificationservice->friendRequestNotification($friendship->getFriend(), $this->getUser());

        return $this->redirectToRoute('app_profilee', ['id' => $user->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/accept_friend_request/{id}', name: 'app_accept_friend_request')]
    public function AcceptFriendRequest(Friendship $friendship): Response
    {
        $friendship->setStatus('accepted');
        $friendship->getUser()->setScore($friendship->getUser()->getScore() + 10);
        $friendship->getFriend()->setScore($friendship->getFriend()->getScore() + 10);

        $this->notificationservice->acceptFriendRequestNotification($friendship);

        $this->em->flush();
        return $this->redirectToRoute('app_profilee', ['id' => $friendship->getUser()->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/reject_friend_request/{id}', name: 'app_reject_friend_request')]
    public function RejectFriendRequest(Friendship $friendship): Response
    {
        $friendship->setStatus('rejected');
        $notification = $this->em->getRepository(Notification::class)->findOneBy([
            'userNotif' => $this->getUser(),
            'toUserNotif' => $friendship->getFriend(),
            'typeNotification' => 'Friend Request Accepted'
        ]) ?? $this->em->getRepository(Notification::class)->findOneBy([
            'userNotif' => $friendship->getFriend(),
            'toUserNotif' => $this->getUser(),
            'typeNotification' => 'Friend Request Accepted'
        ]);

        if ($notification) {
            $this->em->remove($notification);
        }
        $this->em->flush();
        return $this->redirectToRoute('app_profilee', ['id' => $friendship->getUser()->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/unfriend/{id}', name: 'app_unfriend')]
    public function Unfriend(Friendship $friendship): Response
    {
        $this->em->remove($friendship);
        $notification = $this->em->getRepository(Notification::class)->findOneBy([
            'userNotif' => $this->getUser(),
            'toUserNotif' => $friendship->getFriend(),
            'typeNotification' => 'Friend Request Accepted'
        ]) ?? $this->em->getRepository(Notification::class)->findOneBy([
            'userNotif' => $friendship->getFriend(),
            'toUserNotif' => $this->getUser(),
            'typeNotification' => 'Friend Request Accepted'
        ]);

        if ($notification) {
            $this->em->remove($notification);
        }
        $this->em->flush();
        return $this->redirectToRoute('app_profilee', ['id' => $friendship->getUser()->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/cancelRequest/{id}', name: 'app_cancel_request')]
    public function CancelRequest(Friendship $friendship): Response
    {
        $this->em->remove($friendship);
        $notification = $this->em->getRepository(Notification::class)->findOneBy([
            'userNotif' => $this->getUser(),
            'toUserNotif' => $friendship->getFriend(),
            'typeNotification' => 'Friend Request'
        ]);

        if ($notification) {
            $this->em->remove($notification);
        }
        $this->em->flush();
        return $this->redirectToRoute('app_profilee', ['id' => $friendship->getFriend()->getId()]);
    }
}
