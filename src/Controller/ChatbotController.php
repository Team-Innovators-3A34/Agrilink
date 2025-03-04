<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function askChatbot(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        if (empty($userMessage)) {
            return $this->json(['error' => 'Message vide'], 400);
        }

        // Allowed topics (agriculture + AgriLink-related topics)
        $allowedTopics = [
            'agriculture',
            'ferme',
            'recyclage',
            'investissement agricole',
            'engrais',
            'irrigation',
            'sol',
            'culture',
            'récolte',
            'marché agricole',
            'AgriLink',
            'publications',
            'événements',
            'ressources',
            'points de recyclage'
        ];

        // Check if the message is relevant
        $isRelevant = false;
        foreach ($allowedTopics as $topic) {
            if (stripos($userMessage, $topic) !== false) {
                $isRelevant = true;
                break;
            }
        }

        // Reject irrelevant questions
        if (!$isRelevant) {
            return $this->json(['response' => 'Je suis spécialisé en agriculture et AgriLink. Je ne peux pas répondre à cette question.']);
        }

        try {
            // Define a strict prompt to restrict responses
            $context = "Tu es un assistant spécialisé en agriculture et dans l'application AgriLink. "
            . "Réponds uniquement aux questions sur l'agriculture, les cultures, les engrais, "
            . "l'irrigation, l'élevage, l'investissement agricole, et la gestion de l'application AgriLink. "
            . "AgriLink est une plateforme qui connecte trois types d'utilisateurs : les agriculteurs, "
            . "les investisseurs en ressources, et les investisseurs en recyclage. "
            . "Voici ce que les utilisateurs peuvent faire sur AgriLink : "
            . "- Gestion des événements : Les utilisateurs peuvent découvrir et participer à des événements agricoles. "
            . "- Gestion des ressources : Les investisseurs en ressources peuvent ajouter des ressources disponibles "
            . "dans leur profil, et les agriculteurs peuvent envoyer des demandes pour les utiliser. "
            . "- Gestion des publications : Tous les utilisateurs peuvent publier des questions, des offres ou des annonces "
            . "concernant l'agriculture. "
            . "- Gestion des réclamations : Les utilisateurs peuvent soumettre des réclamations et recevoir des réponses. "
            . "- Gestion des points de recyclage : Les investisseurs en recyclage peuvent ajouter des points de recyclage, "
            . "et les agriculteurs peuvent choisir un point pour recycler un produit. "
            . "- Chat : Tous les utilisateurs peuvent discuter entre eux directement. "
            . "- Analyse des plantes : Les agriculteurs peuvent téléverser une photo d'une plante pour analyser si elle est malade ou non. "
            . "Si la question concerne l'un de ces sujets, réponds avec des détails précis. "
            . "Si la question est hors sujet, dis simplement : "
            . "'Je suis spécialisé en agriculture et AgriLink. Je ne peux pas répondre à cette question.'";

            // Make API request to RapidAPI GPT-4
            $response = $this->client->request('POST', 'https://cheapest-gpt-4-turbo-gpt-4-vision-chatgpt-openai-ai-api.p.rapidapi.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-rapidapi-host' => 'cheapest-gpt-4-turbo-gpt-4-vision-chatgpt-openai-ai-api.p.rapidapi.com',
                    'x-rapidapi-key' => $_ENV['RAPIDAPI_KEY'],
                ],
                'json' => [
                    'messages' => [
                        ['role' => 'system', 'content' => $context],
                        ['role' => 'user', 'content' => $userMessage]
                    ],
                    'model' => 'gpt-4o',
                    'max_tokens' => 100,
                    'temperature' => 0.7
                ],
            ]);

            $responseData = $response->toArray();
            $botReply = $responseData['choices'][0]['message']['content'] ?? 'Désolé, je ne peux pas répondre à cette question.';

            return $this->json(['response' => $botReply]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur API: ' . $e->getMessage()], 500);
        }
    }
}
