<?php
// src/Service/FacebookScraperService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookScraperService
{
    private HttpClientInterface $client;
    
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    
    /**
     * Récupère des posts filtrés en continuant à appeler l’API
     * jusqu’à obtenir au moins $limit posts ou qu’il n’y ait plus de pages.
     *
     * @param string|null $cursor Curseur pour la pagination (null pour le premier appel)
     * @param int $limit Nombre de posts désirés
     *
     * @return array ['posts' => array, 'cursor' => string|null]
     */
    public function getPosts(?string $cursor = null, int $limit = 3): array
    {
        $allPosts = [];
        $nextCursor = $cursor;
        $apiLimit = $limit;
        
        while (count($allPosts) < $limit) {
            $url = "https://facebook-scraper3.p.rapidapi.com/search/posts?query=agriculture%20science&limit=" . $apiLimit;
            if ($nextCursor !== null) {
                $url .= "&cursor=" . urlencode($nextCursor);
            }
            
            try {
                $response = $this->client->request('GET', $url, [
                    'headers' => [
                        'x-rapidapi-host' => 'facebook-scraper3.p.rapidapi.com',
                        'x-rapidapi-key'  => 'af15174601msh7a19c0602cb1b7dp1f753fjsn46410f9f7599',
                    ],
                ]);
                $data = $response->toArray();
                $posts = isset($data['results']) && is_array($data['results'])
                    ? $data['results']
                    : [];
                
                // Filtrer les posts indésirables (vérification du nom de l'auteur ET du message)
                $filtered = array_filter($posts, function ($post) {
                    $excludedKeywords = [
                        'india', 'bangla', 'news18', 'bengali', 'west bengal', 'kolkata',
                        'হাই স্কুল', 'hs exam', 'উচ্চ মাধ্যমিক', 'বাংলা', 'ভারত',
                        'sathsara aloka', 'අ.පො.ස'
                    ];
                    $message = strtolower($post['message'] ?? '');
                    $author  = strtolower($post['author']['name'] ?? '');
                    foreach ($excludedKeywords as $keyword) {
                        if (strpos($message, $keyword) !== false || strpos($author, $keyword) !== false) {
                            return false;
                        }
                    }
                    return true;
                });
                
                foreach ($filtered as $post) {
                    $allPosts[] = $this->formatPost($post);
                    if (count($allPosts) >= $limit) {
                        break;
                    }
                }
                
                $nextCursor = $data['cursor'] ?? null;
                if ($nextCursor === null) {
                    break;
                }
            } catch (\Exception $e) {
                break;
            }
        }
        
        return [
            'posts'  => $allPosts,
            'cursor' => $nextCursor,
        ];
    }
    
    /**
     * Formate le post en gérant correctement les images et vidéos.
     */
    private function formatPost(array $post): array
    {
        // Traitement de l'image
        $image = null;
        if (isset($post['image'])) {
            if (is_array($post['image'])) {
                if (isset($post['image']['uri'])) {
                    $image = $post['image']['uri'];
                } else {
                    $images = [];
                    foreach ($post['image'] as $img) {
                        if (is_array($img) && isset($img['uri'])) {
                            $images[] = $img['uri'];
                        } elseif (is_string($img)) {
                            $images[] = $img;
                        }
                    }
                    $image = count($images) === 1 ? $images[0] : $images;
                }
            } else {
                $image = $post['image'];
            }
        }
        
        // Traitement de la vidéo
        $video = null;
        if (isset($post['video'])) {
            if (is_array($post['video'])) {
                if (isset($post['video']['uri'])) {
                    $video = $post['video']['uri'];
                } else {
                    $videos = [];
                    foreach ($post['video'] as $vid) {
                        if (is_array($vid) && isset($vid['uri'])) {
                            $videos[] = $vid['uri'];
                        } elseif (is_string($vid)) {
                            $videos[] = $vid;
                        }
                    }
                    $video = count($videos) === 1 ? $videos[0] : $videos;
                }
            } else {
                $video = $post['video'];
            }
        }
        
        return [
            'id'              => $post['post_id'] ?? null,
            'author'          => [
                'name'                => $post['author']['name'] ?? 'Auteur inconnu',
                'profile_picture_url' => $post['author']['profile_picture_url'] ?? 'https://via.placeholder.com/80',
            ],
            'timestamp'       => isset($post['timestamp']) && is_numeric($post['timestamp'])
                ? date("d/m/Y H:i", (int)$post['timestamp'])
                : 'Date inconnue',
            'message'         => $post['message'] ?? 'Aucun message',
            'image'           => $image,
            'video'           => $video,
            'reactions_count' => $post['reactions_count'] ?? 0,
            'url'             => $post['url'] ?? '#',
        ];
    }
}
