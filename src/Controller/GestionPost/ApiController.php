<?php

namespace App\Controller\GestionPost;

use App\Service\OpenAIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private $openAIService;
    
    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    
    #[Route('/api/generate-tip', name: 'api_generate_tip', methods: ['GET'])]
    public function generateTip(): JsonResponse
    {
        $tip = $this->openAIService->generateAgricultureTip();
        
        return $this->json([
            'tip' => $tip,
        ]);
    }
}