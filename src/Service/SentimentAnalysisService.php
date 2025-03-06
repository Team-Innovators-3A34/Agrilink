<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

class SentimentAnalysisService
{
    private $httpClient;
    
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    
    public function analyzeFrenchText(string $text): array
    {
        try {
            // Try the external API first
            return $this->analyzeFrenchTextWithExternalAPI($text);
        } catch (\Exception $e) {
            // If external API fails, use local fallback
            return $this->analyzeFrenchTextLocally($text);
        }
    }
    
    private function analyzeFrenchTextWithExternalAPI(string $text): array
    {
        
        $response = $this->httpClient->request('POST', 'http://www.sentiment140.com/api/bulkClassifyJson', [
            'json' => [
                'data' => [
                    ['text' => $text]
                ]
            ],
            'timeout' => 3.0
        ]);
        
        $data = $response->toArray();
        
        
        $sentiment = 'neutral';
        $score = 0;
        
        if (isset($data['data'][0]['polarity'])) {
            $polarity = $data['data'][0]['polarity'];
            if ($polarity === 0) {
                $sentiment = 'negative';
                $score = -1;
            } elseif ($polarity === 4) {
                $sentiment = 'positive';
                $score = 1;
            }
        }
        
        return [
            'sentiment' => $sentiment,
            'score' => $score
        ];
    }
    
    private function analyzeFrenchTextLocally(string $text): array
    {
        $text = mb_strtolower($text);
        
        // French positive and negative words
        $positiveWords = ['bien', 'bon', 'super', 'excellent', 'aimer', 'content', 'heureux', 'génial', 
                         'merveilleux', 'agréable', 'parfait', 'bravo', 'felicitations', 'réussi'];
        
        $negativeWords = ['mauvais', 'mal', 'terrible', 'détester', 'triste', 'déçu', 'horrible', 
                         'catastrophe', 'problème', 'difficile', 'pire', 'nul', 'échec'];
        
        $positiveScore = 0;
        $negativeScore = 0;
        
        foreach ($positiveWords as $word) {
            if (mb_strpos($text, $word) !== false) {
                $positiveScore++;
            }
        }
        
        foreach ($negativeWords as $word) {
            if (mb_strpos($text, $word) !== false) {
                $negativeScore++;
            }
        }
        
        if ($positiveScore > $negativeScore) {
            return [
                'sentiment' => 'positive',
                'score' => min(1.0, $positiveScore * 0.2) // Scale to max 1.0
            ];
        } elseif ($negativeScore > $positiveScore) {
            return [
                'sentiment' => 'negative',
                'score' => -min(1.0, $negativeScore * 0.2) // Scale to max -1.0
            ];
        } else {
            return [
                'sentiment' => 'neutral',
                'score' => 0
            ];
        }
    }
}