<?php
// src/Service/TranslationService.php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TranslationService
{
    private $client;
    private $apiUrl;
    private $apiKey;
    private $apiHost;

    public function __construct(string $apiUrl, string $apiKey, string $apiHost)
    {
        $this->apiUrl  = $apiUrl;
        $this->apiKey  = $apiKey;
        $this->apiHost = $apiHost;
        $this->client  = new Client();
    }

    /**
     * Traduit un texte dans la langue cible.
     *
     * @param string $text Texte à traduire.
     * @param string $targetLanguage Code de la langue cible (ex. "en" ou "es").
     * @return string|null Le texte traduit ou null en cas d’échec.
     */
    public function translateText(string $text, string $targetLanguage): ?string
    {
        if (trim($text) === '') {
            return '';
        }

        try {
            $response = $this->client->request('POST', $this->apiUrl, [
                'headers' => [
                    'Content-Type'    => 'application/x-www-form-urlencoded',
                    'x-rapidapi-key'  => $this->apiKey,
                    'x-rapidapi-host' => $this->apiHost,
                ],
                'form_params' => [
                    'text'   => $text,
                    'target' => $targetLanguage,
                ],
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            // Pour débogage (à supprimer en production)
            error_log("API Response for '$text': " . print_r($data, true));

            if (isset($data['translation']) && !empty($data['translation'])) {
                return $data['translation'];
            }
        } catch (GuzzleException $e) {
            error_log("Translation error: " . $e->getMessage());
        }

        return null;
    }
}
