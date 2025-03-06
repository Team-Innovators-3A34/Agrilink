<?php

namespace App\Controller\GestionReclamation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

class PlanteController extends AbstractController
{
    private HttpClientInterface $client;
    private string $rapidApiKey;

    public function __construct(HttpClientInterface $client, string $rapidApiKey)
    {
        $this->client = $client;
        $this->rapidApiKey = $rapidApiKey;
    }

    #[Route('/predict', name: 'plante_predict', methods: ['GET', 'POST'])]
    public function predict(Request $request): Response
    {
        // Traitement du formulaire de prédiction
        if ($request->isMethod('POST')) {
            $file = $request->files->get('image');
            if (!$file) {
                return new Response('Aucun fichier fourni', Response::HTTP_BAD_REQUEST);
            }
            
            // Préparer l'image pour l'affichage (encodage en Base64)
            $imageContent = file_get_contents($file->getPathname());
            $base64Image = base64_encode($imageContent);
            $imageDataUrl = 'data:' . $file->getMimeType() . ';base64,' . $base64Image;
            
            // Envoi du fichier à l'API externe pour la prédiction
            $response = $this->client->request('POST', 'http://localhost:5000/predict', [
                'body' => [
                    'file' => fopen($file->getPathname(), 'r'),
                ],
            ]);
            
            $data = $response->toArray();
            $prediction = $data['prediction'];
            
            // Récupérer automatiquement les informations sur la maladie prédite
            $prompt = "Donne-moi des informations détaillées sur la maladie '$prediction' des plantes, en indiquant ses causes, ses symptômes, comment la traiter et en donnant des conseils pratiques pour les agriculteurs.";
            $diseaseInfo = $this->getDiseaseInfo($prompt);
            
            return $this->render('frontoffice/plante/index.html.twig', [
                'prediction'    => $prediction,
                'imageDataUrl'  => $imageDataUrl,
                'diseaseInfo'   => $diseaseInfo,
            ]);
        }

        // Affichage du formulaire en GET
        return $this->render('frontoffice/plante/index.html.twig');
    }

    #[Route('/search-disease', name: 'search_disease', methods: ['POST'])]
    public function searchDisease(Request $request): Response
    {
        $diseaseName = $request->request->get('disease_name');
        if (!$diseaseName) {
            return new Response('Veuillez entrer le nom d\'une maladie.', Response::HTTP_BAD_REQUEST);
        }

        $prompt = "Donne-moi des informations détaillées sur la maladie '$diseaseName' des plantes, en indiquant ses causes, ses symptômes, comment la traiter et en donnant des conseils pratiques pour les agriculteurs.";
        $diseaseInfo = $this->getDiseaseInfo($prompt);

        return $this->render('frontoffice/plante/_disease_info.html.twig', [
            'diseaseName' => $diseaseName,
            'diseaseInfo' => $diseaseInfo,
        ]);
    }


    /**
     * Appelle l'API ChatGPT (via RapidAPI) pour obtenir des informations sur la maladie.
     */
    private function getDiseaseInfo(string $prompt): ?string
    {
        try {
            $apiResponse = $this->client->request('POST', 'https://chatgpt-42.p.rapidapi.com/chatgpt', [
                'headers' => [
                    'Accept'            => 'application/json',
                    'Content-Type'      => 'application/json',
                    'x-rapidapi-key'    => $this->rapidApiKey,
                    'x-rapidapi-host'   => 'chatgpt-42.p.rapidapi.com',
                ],
                'json' => [
                    'messages'   => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'web_access' => false,
                ],
            ]);

            $content = $apiResponse->toArray();
            if (isset($content['result']) && !empty($content['result'])) {
                return $content['result'];
            } else {
                $this->addFlash('danger', 'Aucune information trouvée pour la maladie.');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur API : ' . $e->getMessage());
        }
        return null;
    }
}