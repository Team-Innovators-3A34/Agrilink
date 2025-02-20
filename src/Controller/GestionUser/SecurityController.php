<?php

namespace App\Controller\GestionUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
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
        throw new \Exception('This route should be handled by the `json_login` firewall.');
    }
}
