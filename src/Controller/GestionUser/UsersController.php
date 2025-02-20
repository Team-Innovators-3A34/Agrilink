<?php

namespace App\Controller\GestionUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/dashboard/users', name: 'app_dashboard_users')]
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findBy(['status' => 'show']);

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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/delete/{id}', name: 'app_delete_users')]
    public function deleteUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $user->setStatus('hide');
        $this->em->flush();
        return $this->redirectToRoute("app_dashboard_users");
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/reject/{id}', name: 'app_reject_users')]
    public function rejectUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $user->setStatus('rejected');
        $this->em->persist($user);
        $this->em->flush();
        return $this->redirectToRoute("app_dashboard_users");
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/update/{id}', name: 'app_update_users')]
    public function updateUsers(String $id, Request $request): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $form = $this->createForm(UserType::class, $user, [
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute("app_dashboard_users");
        }

        return $this->render('backoffice/users/updateUsers.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/detail/{id}', name: 'app_detail_users')]
    public function detailUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);

        return $this->render('backoffice/users/userDetail.html.twig', [
            'user' => $user,
        ]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/profilee/{id}', name: 'app_profilee')]
    public function profile(User $user): Response
    {
        return $this->render('frontoffice/profile/profile.html.twig', [
            'user' => $user,
        ]);
    }
}
