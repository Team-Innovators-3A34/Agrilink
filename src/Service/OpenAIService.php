<?php
namespace App\Service;

use OpenAI;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OpenAIService
{
    private $client;
    private $fallbackService;

    public function __construct(ParameterBagInterface $params, FallbackTipService $fallbackService)
    {
        // Get API key from environment variables
        $apiKey = $params->get('app.openai_api_key') ? $params->get('app.openai_api_key') : '';
        $this->fallbackService = $fallbackService;
    
    if ($apiKey) {
        $this->client = OpenAI::client($apiKey);
    }
    }
    
    public function generateAgricultureTip(string $prompt = "Donne un conseil agricole utile du jour en une phrase concise:"): string
    {
        if (!$this->client) {
            return $this->fallbackService->getRandomTip();
        }
        try {
            $response = $this->client->completions()->create([
                'model' => 'gpt-3.5-turbo-instruct', // Using a model that works with completions API
                'prompt' => $prompt,
                'max_tokens' => 100,
                'temperature' => 0.7,
            ]);
            
            return trim($response['choices'][0]['text']);
        } catch (\Exception $e) {
            // Fallback in case of API error
            return $this->fallbackService->getRandomTip();
        }
    }
}