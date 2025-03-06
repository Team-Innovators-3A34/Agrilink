<?php

namespace App\Controller\GestionReclamation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JobController extends AbstractController
{
    private HttpClientInterface $client;
    private string $apiUrl;
    private string $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiUrl = 'https://jooble.org/api/';
        // Remplacez par votre clé API Jooble
        $this->apiKey = 'bbfdbec0-7070-4112-a635-d87b2f5e6c07';
    }

   
    #[Route('/jobs', name: 'jobs_list')]
    public function index(Request $request): Response
    {
        // Récupérer la page courante (par défaut 1)
        $page = $request->query->getInt('page', 1);
        // Récupérer le filtre de pays (location)
        $country = $request->query->get('country');

        // Construire le corps de la requête
        $body = [
            'keywords' => 'agriculture',
            'page' => $page,
        ];

        // Ajouter le filtre par pays si renseigné
        if (!empty($country)) {
            $body['location'] = $country;
        }

        // Appel de l'API Jooble
        $response = $this->client->request('POST', $this->apiUrl . $this->apiKey, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $body,
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération des offres (code ' . $response->getStatusCode() . ')');
        }

        // Transformation de la réponse JSON en tableau associatif
        $data = $response->toArray();
        $jobs = $data['jobs'] ?? [];
        $totalCount = $data['totalCount'] ?? 0;

        return $this->render('job/index.html.twig', [
            'jobs' => $jobs,
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'perPage' => count($jobs),
            'country' => $country,
        ]);
    }
}
