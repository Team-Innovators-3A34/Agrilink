<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Log\LoggerInterface;

class TikTokService
{
    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private FilesystemAdapter $cache;
    private string $apiKey = 'b87ca822cdmsh0a0c4c3c7567bd9p1824ddjsn90c4848fb6c4';

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->cache  = new FilesystemAdapter();
    }

    public function getAgricultureVideos(int $count = 20, int $cursor = 0): array
    {
        $cacheKey = 'tiktok_videos_' . $count . '_' . $cursor;
        $cacheItem = $this->cache->getItem($cacheKey);
    
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }
    
        try {
            $response = $this->client->request('GET', 'https://tiktok-scraper7.p.rapidapi.com/feed/search', [
                'headers' => [
                    'x-rapidapi-host' => 'tiktok-scraper7.p.rapidapi.com',
                    'x-rapidapi-key'  => $this->apiKey,
                ],
                'query' => [
                    'keywords'     => 'agriculture,farming,crops,organic farming,agrotech',
                    'region'       => 'us',
                    'count'        => $count,
                    'cursor'       => $cursor,
                    'publish_time' => 0,
                    'sort_type'    => 0,
                ],
                'timeout' => 15,
            ]);
    
            $data = $response->toArray();
    
            // Vérifiez si le cursor a changé pour obtenir de nouvelles vidéos
            $videos = array_map(function ($video) {
                $videoId  = $video['video_id'] ?? null;
                $username = $video['author']['unique_id'] ?? null;
                return [
                    'id'         => $videoId ?: ($video['aweme_id'] ?? bin2hex(random_bytes(4))),
                    'username'   => $username,
                    'title'      => $video['title'] ?? 'Titre non disponible',
                    'embed_url'  => ($videoId && $username)
                        ? "https://www.tiktok.com/embed/@{$username}/video/{$videoId}"
                        : null,
                    'cover'      => $video['cover'] ?? null,
                    'video_url'  => $video['play'] ?? null,
                    'region'     => $video['region'] ?? 'Région inconnue',
                    'duration'   => $video['duration'] ?? 0,
                    'hashtags'   => $this->extractHashtags($video['title'] ?? '')
                ];
            }, $data['data']['videos'] ?? []);
    
            // Mettez à jour le cache avec les nouvelles vidéos
            $nextCursor = $data['data']['cursor'] ?? 0;
            $hasMore = $data['data']['hasMore'] ?? false;

            $result = [
                'videos'  => $videos,
                'cursor'  => $nextCursor,
                'hasMore' => $hasMore,
            ];

            $cacheItem->set($result)->expiresAfter(1800); // Mise en cache 30 minutes
            $this->cache->save($cacheItem);
    
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('TikTok API Error: ' . $e->getMessage());
            return [
                'videos'  => [],
                'cursor'  => 0,
                'hasMore' => false,
            ];
        }
    }

    private function extractHashtags(string $title): array
    {
        preg_match_all('/#([\p{L}\d_]+)/u', $title, $matches);
        return array_slice(array_unique($matches[1] ?? []), 0, 5);
    }
}