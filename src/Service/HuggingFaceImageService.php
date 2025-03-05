<?php
namespace App\Service;

use GuzzleHttp\Client;


class HuggingFaceImageService
{
    private $client;
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
    }

    public function generateImageFromDescription(string $description)
    {
        $response = $this->client->post('https://api-inference.huggingface.co/models/runwayml/stable-diffusion-v1-5', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'json' => [
                'inputs' => $description
            ],
        ]);

        // Create directory structure if it doesn't exist
        $uploadDir = 'public/uploads/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Traitement de l'image binaire
        $imageData = $response->getBody()->getContents();
        $filename = 'generated_' . md5(uniqid()) . '.jpg';
        $path = 'uploads/images/' . $filename;
        file_put_contents($uploadDir . basename($path), $imageData);
        
        return $path;
    }
}