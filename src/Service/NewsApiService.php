<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NewsApiService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = 'b87ca822cdmsh0a0c4c3c7567bd9p1824ddjsn90c4848fb6c4'; // Clé API
    }

    public function getAgricultureNews($page = 1)
    {
        // Requête à l'API avec gestion de la pagination
        $response = $this->client->request('GET', 'https://news-api14.p.rapidapi.com/v2/search/articles', [
            'headers' => [
                'x-rapidapi-host' => 'news-api14.p.rapidapi.com',
                'x-rapidapi-key' => $this->apiKey,
            ],
            'query' => [
                'query'    => 'agriculture',
                'language' => 'en',
                'page'     => $page,
            ],
        ]);

        return $response->toArray();
    }
}
