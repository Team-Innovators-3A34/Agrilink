<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpClient\HttpClient;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If the user is already logged in, redirect based on role & status
        if ($this->getUser()) {
            $user = $this->getUser();


            if ($user->getStatus() != 'approved') {
                $this->addFlash('error', 'Your account is not Approved. You will be notified by email once your account is approved.');
                return $this->redirectToRoute('app_login');
            }

            // Check status (assuming status is a string like 'active', 'pending', etc.)
            if ($user->getAccountVerification() !== 'approved' && $user->getStatus() == 'approved') {
                $this->addFlash('error', 'Your account is not active. Please Try to Active it or contact Support.');
                return $this->redirectToRoute('app_send_code_validation');
            }


            // Redirect based on role
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('app_dashboard');
            } else {
                return $this->redirectToRoute('app_home');
            }


            // Default fallback route
            return $this->redirectToRoute('homepage');
        }

        // Get login error (if any)
        $error = $authenticationUtils->getLastAuthenticationError();
        // Get last entered username
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(): Response
    {
        // This route is handled by the `json_login` firewall in security.yaml
        // No need to implement logic here
        throw new \Exception('This route should be handled by the `json_login` firewall.');
    }
}
