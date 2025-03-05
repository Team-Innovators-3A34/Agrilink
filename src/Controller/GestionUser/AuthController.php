<?php

namespace App\Controller\GestionUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface; // Change this line
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class AuthController extends AbstractController
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager) // Change the type-hint here
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            throw new AuthenticationException('Invalid credentials');
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
