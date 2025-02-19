<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailerService;
use phpDocumentor\Reflection\Types\This;

final class UsersController extends AbstractController
{
    private MailerService $mailerService;
    private EntityManagerInterface $em;

    public function __construct(MailerService $mailerService, EntityManagerInterface $em)
    {
        $this->mailerService = $mailerService;
        $this->em = $em;
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users', name: 'app_dashboard_users')]
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findALL();

        return $this->render('backoffice/users/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}', name: 'app_accept_users')]
    public function acceptUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $user->setStatus('approved');
        $this->em->persist($user);
        $this->em->flush();
        $this->mailerService->sendAccountVerificationEmail($user->getEmail(), $user);
        return $this->redirectToRoute("app_dashboard_users");
    }
}
