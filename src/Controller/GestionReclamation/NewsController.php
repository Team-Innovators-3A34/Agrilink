<?php

namespace App\Controller\GestionReclamation;

use App\Service\NewsApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    private $newsApiService;

    public function __construct(NewsApiService $newsApiService)
    {
        $this->newsApiService = $newsApiService;
    }

    // Route principale pour afficher la première page d'articles
    #[Route('/agriculture-news', name: 'agriculture_news')]
    public function showAgricultureNews(): Response
    {
        $news = $this->newsApiService->getAgricultureNews();

        return $this->render('frontoffice/new/index.html.twig', [
            'news' => $news['data'] ?? [],
        ]);
    }

    // Route pour charger plus d'articles via AJAX
    #[Route('/load-more-news', name: 'load_more_news')]
    public function loadMoreNews(Request $request): Response
    {
        $page = $request->query->get('page', 1);
        $news = $this->newsApiService->getAgricultureNews($page);

        if (!empty($news['data'])) {
            return $this->render('frontoffice/new/_news_items.html.twig', [
                'news' => $news['data'],
            ]);
        }

        // Retourne une réponse vide s'il n'y a plus d'articles
        return new Response('');
    }
}
