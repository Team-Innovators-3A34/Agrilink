<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class InfobipSmsSender
{
    private HttpClientInterface $client;
    private string $apiUrl;
    private string $apiKey;
    private string $sender;

    public function __construct(HttpClientInterface $client, string $apiUrl, string $apiKey, string $sender)
    {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->sender = $sender;
    }

    public function sendSms(string $to, string $text): array
    {
        $payload = [
            'messages' => [
                [
                    'destinations' => [
                        ['to' => $to]
                    ],
                    'from' => $this->sender,
                    'text' => $text,
                ]
            ]
        ];

        try {
            $response = $this->client->request('POST', $this->apiUrl, [
                'headers' => [
                    'Authorization' => 'App ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
            ]);

            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            throw new \Exception("Erreur lors de l'envoi du SMS : " . $e->getMessage());
        }
    }
}
