<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;


class ContentModerationService
{
    private $httpClient;
    private $perspectiveApiKey;
    private $logger;

    public function __construct(
        HttpClientInterface $httpClient, 
        string $perspectiveApiKey,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->perspectiveApiKey = $perspectiveApiKey;
        $this->logger = $logger;
    }

    public function analyzeComment(string $commentText): array
    {
        // Extensive logging
        $this->logger->emergency('CONTENT MODERATION DEBUG', [
            'comment' => $commentText,
            'api_key_length' => strlen($this->perspectiveApiKey),
            'api_key_starts_with' => substr($this->perspectiveApiKey, 0, 5)
        ]);

        try {
            $response = $this->httpClient->request('POST', 'https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze', [
                'query' => ['key' => $this->perspectiveApiKey],
                'json' => [
                    'comment' => ['text' => $commentText],
                    'requestedAttributes' => [
                        'TOXICITY' => [],
                        'PROFANITY' => [],
                        'SEVERE_TOXICITY' => []
                    ]
                ]
            ]);

            $data = $response->toArray();

            // Log full API response
            $this->logger->emergency('PERSPECTIVE API FULL RESPONSE', [
                'raw_response' => $data
            ]);

            return [
                'toxicity' => $data['attributeScores']['TOXICITY']['summaryScore']['value'] ?? 0,
                'profanity' => $data['attributeScores']['PROFANITY']['summaryScore']['value'] ?? 0,
                'severe_toxicity' => $data['attributeScores']['SEVERE_TOXICITY']['summaryScore']['value'] ?? 0
            ];
        } catch (\Exception $e) {
            // Comprehensive error logging
            $this->logger->emergency('CONTENT MODERATION ERROR', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'comment' => $commentText
            ]);

            return [
                'toxicity' => 0,
                'profanity' => 0,
                'severe_toxicity' => 0
            ];
        }
    }

    // Enhanced bad words detection
    public function containsBadWords(string $commentText): bool
    {
        $badWords = [
            // Comprehensive bad word list
            'fuck', 'shit', 'cunt', 'bitch', 'asshole', 
            'merde', 'con', 'putain', 'salope',
            'dick', 'cock', 'pussy', 'bastard',
            'enculé', 'connard', 'pute'
        ];

        $lowercaseComment = strtolower($commentText);
        
        foreach ($badWords as $word) {
            if (stripos($lowercaseComment, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    // Manual toxicity scoring
    public function manualToxicityScore(string $commentText): float
    {
        $toxicityScore = 0;

        // Bad words multiplier
        if ($this->containsBadWords($commentText)) {
            $toxicityScore += 0.7;
        }

        // Length and intensity check
        if (strlen($commentText) > 50 && preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $commentText)) {
            $toxicityScore += 0.3;
        }

        return min($toxicityScore, 1.0);
    }
}