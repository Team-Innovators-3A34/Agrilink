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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;


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
        $form = $this->createForm(UserType::class, $user, [
            'attr' => ['novalidate' => 'novalidate']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),  // Directory defined in parameters
                        $filename
                    );
                    $user->setImage($filename);
                } catch (\Exception $e) {
                    // Handle the exception if something goes wrong
                    $this->addFlash('error', 'Failed to upload image.');
                }
            }

            $this->em->flush();
            $this->addFlash('success', 'Profile updated successfully!');

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

    #[Route('/settings/Adress', name: 'app_address')]
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    public function myAdress(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Adress updated successfully!');
            return $this->redirectToRoute('app_address');
        }
        return $this->render('frontoffice/settings/adress/adress.html.twig', ["form" => $form->createView()]);
    }


    #[Route('/check-email', name: 'app_check_email', methods: ['POST'])]
    public function checkEmail(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['exists' => false]);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        return new JsonResponse(['exists' => $user !== null]);
    }

    public function updateProfile(Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = [];
            if (!$form->isValid()) {
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                return new JsonResponse(['success' => false, 'errors' => $errors]);
            }

            $submittedEmail = $form->get('email')->getData();

            if ($submittedEmail !== $user->getEmail()) {
                $existingUser = $this->em
                    ->getRepository(User::class)
                    ->findOneBy(['email' => $submittedEmail]);

                if ($existingUser) {
                    return new JsonResponse(['success' => false, 'errors' => ['Email is already taken.']]);
                }
            }

            $this->em->persist($user);
            $this->em->flush();

            return new JsonResponse(['success' => true, 'message' => 'Profile updated successfully.']);
        }

        return $this->render('frontoffice/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
