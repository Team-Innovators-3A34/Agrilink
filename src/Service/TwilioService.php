<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private $twilio;

    public function __construct(string $twilioSid, string $twilioAuthToken)
    {
        $this->twilio = new Client($twilioSid, $twilioAuthToken);
    }

    public function sendSms(string $to, string $message): void
    {
        $this->twilio->messages->create(
            $to, // Numéro destinataire (ex: "+216XXXXXXXX")
            [
                'from' => $_ENV['TWILIO_PHONE_NUMBER'],
                'body' => $message
            ]
        );
    }
}
