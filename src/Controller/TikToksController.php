<?php

namespace App\Controller;

use App\Service\TikTokService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TikToksController extends AbstractController
{
    #[Route('/tiktok', name: 'tiktok_videos')]
    public function index(Request $request, TikTokService $tikTokService): Response
    {
        // Récupérer le cursor depuis l'URL (ou 0 par défaut)
        $cursor = $request->query->getInt('cursor', 0);
        $perPage = 10; // Nombre de vidéos par page

        // Appeler le service en passant le curseur actuel
        $result = $tikTokService->getAgricultureVideos($perPage, $cursor);

        // Utilisation de l'opérateur null coalescing pour éviter l'erreur Undefined array key "videos"
        $videos = $result['videos'] ?? [];
        $nextCursor = $result['cursor'] ?? 0;
        $hasMore = $result['hasMore'] ?? false;

        // Si la requête est AJAX, renvoyer uniquement le rendu partiel
        if ($request->isXmlHttpRequest()) {
            return $this->render('tik_tok/_videos.html.twig', [
                'videos' => $videos,
            ]);
        }

        return $this->render('tik_tok/index.html.twig', [
            'videos'     => $videos,
            'cursor'     => $cursor,
            'nextCursor' => $hasMore ? $nextCursor : null,
        ]);
    }
}