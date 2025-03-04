<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;

class MatchService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getMatches(int $userId): array
    {
        try {
            $response = $this->client->request(
                'GET',
                'http://127.0.0.1:5000/match',
                ['query' => ['user_id' => $userId]]
            );

            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            return ['error' => 'API Flask non accessible : ' . $e->getMessage()];
        } catch (ClientExceptionInterface | ServerExceptionInterface $e) {
            return ['error' => 'Erreur côté serveur Flask : ' . $e->getMessage()];
        } catch (DecodingExceptionInterface $e) {
            return ['error' => 'Réponse JSON invalide : ' . $e->getMessage()];
        }
    }
}
