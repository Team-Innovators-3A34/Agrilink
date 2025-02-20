<?php
namespace App\Service;

use GuzzleHttp\Client;

class HuggingFaceService
{
    private $client;
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
    }

    public function classifyRequest(string $requestText)
    {
        $response = $this->client->post('https://api-inference.huggingface.co/models/bert-base-uncased', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'json' => [
                'inputs' => $requestText
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        return $body;  // Retourne les résultats de la classification
    }

    public function generateResponse(string $inputMessage)
    {
        $response = $this->client->post('https://api-inference.huggingface.co/models/gpt-2', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'json' => [
                'inputs' => $inputMessage
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        return $body[0]['generated_text'];  // Retourne le texte généré par GPT-2
    }
}
