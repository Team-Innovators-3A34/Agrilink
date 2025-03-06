<?php
// src/Controller/GoogleController.php

namespace App\Controller\GestionUser;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppAuthenticator; // Authenticator personnalisé

class GoogleController extends AbstractController
{
    private $em;

    private $mailer;

    public function __construct(EntityManagerInterface $em, \App\Service\MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(
        Request $request,
        ClientRegistry $clientRegistry,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator // Ajout du custom authenticator
    ) {
        $client = $clientRegistry->getClient('google');

        try {
            $accessToken = $client->getAccessToken();
            $googleUser = $client->fetchUserFromToken($accessToken);

            $email = $googleUser->getEmail();
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Email invalide');
                return $this->redirectToRoute('app_login');
            }


            if ($user->getStatus() === 'hide') {
                $this->addFlash('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
                return $this->redirectToRoute('app_send_code_validation');
            }

            if ($user->getAccountVerification() == 'pending') {
                $this->addFlash('error', 'Votre compte n\'a pas encore été vérifié. Veuillez vérifier votre compte.');
                return $this->redirectToRoute('app_login');
            }


            if ($user->getStatus() == 'approved' && $user->getAccountVerification() != 'approved') {
            }


            if ($user->getLockUntil() && $user->getLockUntil() > new \DateTime()) {
                $this->addFlash('error', 'Votre compte est temporairement verrouillé. Veuillez réessayer plus tard.');
                return $this->redirectToRoute('app_login');
            }

            if ($user->getStatus() == 'hide') {
                $this->addFlash('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
                return $this->redirectToRoute('app_login');
            }

            if ($user->is2FA()) {
                $validationCode = random_int(1000, 9999);
                $user->setCode2FA($validationCode);
                $user->setCode2FAexpiry(new \DateTime('+15 minutes'));
                $this->em->flush();

                // Send the 2FA code via email
                $subject = "Your Verification Code";
                $this->mailer->sendEmail(
                    $user->getEmail(),
                    $subject,
                    'emailTemplates/activationCode.html.twig',
                    [
                        "validationCode" => $validationCode,
                        "display_name" => $user->getNom() . " " . $user->getPrenom(),
                    ]
                );
                $request->getSession()->set('email_for_verification', $user->getEmail());
                $request->getSession()->set('action', '2FA');
                $userAuthenticator->authenticateUser($user, $authenticator, $request);
                return $this->redirectToRoute('app_verify_code_validation');
            }

            $user->setFailedLoginAttempts(null);
            $user->setLockUntil(null);
            $this->em->flush();

            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        } catch (IdentityProviderException $e) {
            return new JsonResponse(['status' => false, "message" => 'Échec de l\'authentification']);
        }
    }
}
