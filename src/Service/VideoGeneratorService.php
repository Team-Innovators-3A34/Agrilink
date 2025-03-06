<?php
// src/Service/VideoGeneratorService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class VideoGeneratorService
{
    private HttpClientInterface $client;
    private string $apiUrl = 'https://text-to-video.p.rapidapi.com/v3/process_text_and_search_media';
    private string $rapidApiKey = 'b87ca822cdmsh0a0c4c3c7567bd9p1824ddjsn90c4848fb6c4';
    private string $rapidApiHost = 'text-to-video.p.rapidapi.com';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function generateVideo(string $description, string $dimension = '16:9'): array
    {
        $payload = [
            'script'    => $description,
            'dimension' => $dimension,
        ];

        $headers = [
            'Content-Type'    => 'application/json',
            'x-rapidapi-host' => $this->rapidApiHost,
            'x-rapidapi-key'  => $this->rapidApiKey,
        ];

        try {
            $response = $this->client->request('POST', $this->apiUrl, [
                'json'    => $payload,
                'headers' => $headers,
            ]);

            // On récupère le contenu si la requête est réussie
            $statusCode = $response->getStatusCode();
            $content    = $response->getContent();

            return [
                'status' => $statusCode,
                'data'   => json_decode($content, true),
            ];
        } catch (ClientExceptionInterface $e) {
            // Ici, on intercepte notamment l'erreur 401 Unauthorized
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 0;
            return [
                'status' => $statusCode,
                'error'  => "Erreur lors de l'appel à l'API: " . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Gestion de toute autre exception
            return [
                'status' => 500,
                'error'  => "Une erreur inattendue est survenue: " . $e->getMessage(),
            ];
        }
    }
}
