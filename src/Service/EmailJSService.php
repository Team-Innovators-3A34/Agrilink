<?php
namespace App\Service;

use GuzzleHttp\Client;
class EmailJSService
{
    private $client;
    private $userId;
    private $serviceId;
    private $templateId;

    public function __construct(string $userId, string $serviceId, string $templateId)
    {
        $this->client = new Client();
        $this->userId = $userId;
        $this->serviceId = $serviceId;
        $this->templateId = $templateId;
    }

    public function sendEmail(array $params): bool
    {
        try {
            $response = $this->client->post('https://api.emailjs.com/api/v1.0/email/send', [
                'json' => [
                    'service_id' => $this->serviceId,
                    'template_id' => $this->templateId,
                    'user_id' => $this->userId,
                    'template_params' => $params,
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            // Gérer l'erreur ici
            return false;
        }
    }
}
