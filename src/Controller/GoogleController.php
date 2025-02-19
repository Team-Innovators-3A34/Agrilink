<?php
// src/Controller/GoogleController.php

namespace App\Controller;

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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
                $this->addFlash('error', 'Invalid Credentials');
                return $this->redirectToRoute('app_login');
            }

            if ($user->getStatus() != 'approved') {
                $this->addFlash('error', 'Your account is not active. Please Try to Active it or contact Support.');
                return $this->redirectToRoute('app_send_code_validation');
            }

            // 🔹 Connexion automatique de l'utilisateur
            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        } catch (IdentityProviderException $e) {
            return new JsonResponse(['status' => false, "message" => 'Authentication failed']);
        }
    }
}
