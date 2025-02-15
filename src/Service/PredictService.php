<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PredictService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function predictCropYield(array $data): float
    {
        try {
            // Convertir les booléens pour Flask
            $data['Fertilizer_Used'] = (bool)$data['Fertilizer_Used'];
            $data['Irrigation_Used'] = (bool)$data['Irrigation_Used'];

            error_log("Payload envoyé à Flask : " . json_encode($data));

            $response = $this->client->request('POST', 'http://localhost:5000/prediction', [
                'json' => $data,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $content = $response->toArray();
            error_log("Réponse de Flask : " . print_r($content, true));

            if (!isset($content['prediction'])) {
                throw new \Exception("La réponse de Flask ne contient pas de clé 'prediction'. Réponse complète : " . json_encode($content));
            }

            return (float) $content['prediction'];

        } catch (\Exception $e) {
            error_log("ERREUR CRITIQUE : " . $e->getMessage());
            throw $e;
        }
    }
}
