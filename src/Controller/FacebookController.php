<?php
// src/Controller/FacebookController.php
namespace App\Controller;

use App\Service\FacebookScraperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
    private FacebookScraperService $facebookService;
    
    public function __construct(FacebookScraperService $facebookService)
    {
        $this->facebookService = $facebookService;
    }
    
    #[Route('/facebook/posts', name: 'facebook_posts')]
    public function index(): Response
    {
        // Charger 12 posts au départ
        $data = $this->facebookService->getPosts(null, 12);
        return $this->render('facebook/posts.html.twig', [
            'posts'  => $data['posts'],
            'cursor' => $data['cursor'],
        ]);
    }
    
    #[Route('/facebook/posts/more/{cursor?}', name: 'facebook_posts_more', methods: ['GET'])]
    public function morePosts(?string $cursor = null): JsonResponse
    {
        // Charger 3 posts supplémentaires
        return $this->json($this->facebookService->getPosts($cursor, 3));
    }
}
