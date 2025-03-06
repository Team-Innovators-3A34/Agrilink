<?php

namespace App\Controller\GestionReclamation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FruitController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // Page d'accueil pour la prédiction du type de fruit/légume et calories
    #[Route('/fruit', name: 'fruit_index')]
    public function index(): Response
    {
        return $this->render('frontoffice/fruit/index.html.twig', [
            'prediction'   => null,
            'imageDataUrl' => null, // On initialise la variable pour éviter l'erreur Twig
        ]);
    }

    // Méthode pour prédire le type et les calories en appelant /predict/fruit
    #[Route('/fruit/predict', name: 'fruit_predict', methods: ['POST'])]
    public function predict(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            $this->addFlash('error', 'Veuillez envoyer une image.');
            return $this->redirectToRoute('fruit_index');
        }

        // Générer l'URL de l'image en base64 afin de l'afficher dans la vue
        $imageDataUrl = "data:" . $file->getMimeType() . ";base64," . base64_encode(file_get_contents($file->getPathname()));

        // Envoi du fichier en utilisant l'option "body"
        $response = $this->client->request('POST', 'http://127.0.0.1:5110/predict/fruit', [
            'body' => [
                'file' => fopen($file->getPathname(), 'r'),
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            $this->addFlash('error', "Erreur lors de la communication avec le modèle de fruits.");
            return $this->redirectToRoute('fruit_index');
        }

        $data = $response->toArray();

        return $this->render('frontoffice/fruit/index.html.twig', [
            'prediction'   => $data,
            'imageDataUrl' => $imageDataUrl,
        ]);
    }

    // Page d'accueil pour la prédiction de la qualité du fruit
    #[Route('/quality', name: 'quality_index')]
    public function qualityIndex(): Response
    {
        return $this->render('frontoffice/fruit/quality.html.twig', [
            'prediction' => null,
        ]);
    }

    // Méthode pour prédire la qualité en appelant /predict/quality
    #[Route('/quality/predict', name: 'quality_predict', methods: ['POST'])]
    public function predictQuality(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            $this->addFlash('error', 'Veuillez envoyer une image.');
            return $this->redirectToRoute('quality_index');
        }

        $response = $this->client->request('POST', 'http://127.0.0.1:5110/predict/quality', [
            'body' => [
                'file' => fopen($file->getPathname(), 'r'),
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            $this->addFlash('error', "Erreur lors de la communication avec le modèle de qualité.");
            return $this->redirectToRoute('quality_index');
        }

        $data = $response->toArray();

        return $this->render('frontoffice/fruit/quality.html.twig', [
            'prediction' => $data,
        ]);
    }
}
