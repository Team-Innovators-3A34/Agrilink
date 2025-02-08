<?php

namespace App\Controller\GestionUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UpdateProfilePasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/settings', name: 'app_settings')]
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('frontoffice/settings/settings.html.twig', [
            'controller_name' => 'SettingsController',
            'user' => $user,
        ]);
    }
    
    #[Route('/settings/profile', name: 'app_profile')]
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    public function updateAccountInformation(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the file upload
            $file = $form->get('image')->getData();
            if ($file) {
                // Generate a unique filename
                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'),  // Directory defined in parameters
                        $filename
                    );
                    // Update the user's image property with the filename
                    $user->setImage($filename);
                } catch (\Exception $e) {
                    // Handle the exception if something goes wrong
                    $this->addFlash('error', 'Failed to upload image.');
                }
            }

            // Save the updated user entity
            $this->em->flush();
            $this->addFlash('success', 'Profile updated successfully!');

            // Redirect to the same page (to avoid resubmission on refresh)
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('frontoffice/settings/profile/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/settings/password', name: 'app_change_password')]
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    public function updatePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdateProfilePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmNewPassword = $form->get('confirmNewPassword')->getData();

            if ($newPassword !== $confirmNewPassword) {
                $this->addFlash('error', 'Passwords do not match !');
                return $this->redirectToRoute('app_change_password');
            }

            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $this->em->flush();
                $this->addFlash('success', 'Password updated Succesfuly !');
            } else {
                $this->addFlash('error', 'Old password is incorrect !');
            }
        }

        return $this->render('frontoffice/settings/password/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
