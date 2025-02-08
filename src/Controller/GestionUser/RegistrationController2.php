<?php

namespace App\Controller\GestionUser;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NotificationService;



class RegistrationController2 extends AbstractController
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the value of the "user_type" field
            $userType = $form->get('user_type')->getData(); // Use the form field data

            // Assign a role based on the user type
            switch ($userType) {
                case 'agriculture':
                    $user->setRoles(['ROLE_AGRICULTURE', 'ROLE_USER']);
                    $user->setImage('agriculteur.png');
                    break;
                case 'agricultural_resource_investor':
                    $user->setRoles(['ROLE_RESOURCE_INVESTOR', 'ROLE_USER']);
                    $user->setImage('ressource.png');
                    break;
                case 'recycling_investor':
                    $user->setRoles(['ROLE_RECYCLING_INVESTOR', 'ROLE_USER']);
                    $user->setImage('pointRecyclage.png');
                    break;
            }

            // Hash the password
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Set initial user status
            $user->setStatus('pending');
            $user->setAccountVerification('pending');


            // Save the user to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Send a notification to the admin
            $this->notificationService->registerNotification($user);

            $this->addFlash('success', 'Your account has been created. Please wait for approval.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
