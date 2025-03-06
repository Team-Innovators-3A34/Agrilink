<?php

namespace App\Service;

use App\Entity\Friendship;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class FriendshipService
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function getFriendship($user)
    {
        $currentUser = $this->security->getUser();
        $friendship = $this->em->getRepository(Friendship::class)->findOneBy([
            'user' => $user, 
            'friend' => $currentUser
        ]);

        if (!$friendship) {
            $friendship = $this->em->getRepository(Friendship::class)->findOneBy([
                'friend' => $user, 
                'user' => $currentUser
            ]);
        }

        return $friendship;
    }

    public function updateExpiredRequests()
    {
        $currentUser = $this->security->getUser();
        $demandes = $currentUser->getDemandes();

        foreach ($demandes as $demande) {
            if ($demande->getStatus() === 'en cours' && $demande->getExpireDate() < new \DateTime()) {
                $demande->setStatus('terminé');
                $this->em->persist($demande);
            }
        }

        $this->em->flush();
        return $demandes;
    }
}
