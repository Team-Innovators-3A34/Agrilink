<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DiseaseRecognitionService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function predictDisease(UploadedFile $image): string
    {
        try {
            // Ouvrir l'image pour l'envoyer en multipart/form-data
            $response = $this->client->request('POST', 'http://localhost:5001/prediction', [
                'headers' => ['Accept' => 'application/json'],
                'body' => [
                    'file' => fopen($image->getPathname(), 'r')
                ]
            ]);

            $content = $response->toArray();
            error_log("Réponse de Flask : " . print_r($content, true));

            if (!isset($content['prediction'])) {
                throw new \Exception("La réponse de Flask ne contient pas de clé 'prediction'.");
            }

            return $content['prediction'];

        } catch (\Exception $e) {
            error_log("Erreur critique : " . $e->getMessage());
            throw $e;
        }
    }
}
