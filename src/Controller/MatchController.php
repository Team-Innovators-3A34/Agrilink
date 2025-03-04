<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MatchService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class MatchController extends AbstractController
{
    private MatchService $matchService;
    private $httpClient;


    public function __construct(MatchService $matchService, HttpClientInterface $httpClient)
    {
        $this->matchService = $matchService;
        $this->httpClient = $httpClient;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/matched-users/{userId}', name: 'app_matched_users', methods: ['GET'])]
    public function getMatchedUser(int $userId, EntityManagerInterface $entityManager): JsonResponse
    {
        $url = "http://127.0.0.1:5000/match?user_id=" . $userId;

        try {
            $response = $this->httpClient->request('GET', $url);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
        } catch (\Exception $e) {
            return new JsonResponse(["error" => "Erreur lors de l'appel à Flask", "details" => $e->getMessage()], 500);
        }

        if ($statusCode !== 200) {
            return new JsonResponse(["error" => "Flask a retourné une erreur", "status" => $statusCode], 500);
        }

        // Décodage de la réponse JSON
        $data = json_decode($content, true);
        if (!isset($data['matches']) || !is_array($data['matches']) || empty($data['matches'])) {
            return new JsonResponse(["error" => "Aucun utilisateur correspondant trouvé"], 404);
        }

        // Récupération de tous les utilisateurs correspondants
        $matchedUsers = $entityManager->getRepository(User::class)->findBy(['id' => $data['matches']]);

        if (empty($matchedUsers)) {
            return new JsonResponse(["error" => "Aucun utilisateur trouvé en base"], 404);
        }

        // Retourner tous les utilisateurs trouvés
        $result = [];
        foreach ($matchedUsers as $matchedUser) {
            $result[] = [
                'id' => $matchedUser->getId(),
                'description' => $matchedUser->getDescription(),
                'latitude' => $matchedUser->getLatitude(),
                'longitude' => $matchedUser->getLongitude(),
                'score' => $matchedUser->getScore(),
            ];
        }

        return new JsonResponse($result);
    }
}
