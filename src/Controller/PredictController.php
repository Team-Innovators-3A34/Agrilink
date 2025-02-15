<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PredictController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/predict', name: 'app_predict', methods: ['GET', 'POST'])]
    public function predict(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $data = [
                'Region' => $request->request->get('Region'),
                'Soil_Type' => $request->request->get('Soil_Type'),
                'Crop' => $request->request->get('Crop'),
                'Rainfall_mm' => (float) $request->request->get('Rainfall_mm'),
                'Temperature_Celsius' => (float) $request->request->get('Temperature_Celsius'),
                'Fertilizer_Used' => (bool) $request->request->get('Fertilizer_Used'),
                'Irrigation_Used' => (bool) $request->request->get('Irrigation_Used'),
                'Weather_Condition' => $request->request->get('Weather_Condition'),
                'Days_to_Harvest' => (int) $request->request->get('Days_to_Harvest'),
            ];

            try {
                $response = $this->httpClient->request('POST', 'http://localhost:5000/predict', [
                    'json' => $data,
                ]);

                $responseData = $response->toArray();
                $prediction = $responseData['prediction'] ?? null;

                if ($prediction !== null) {
                    $session->set('prediction', $prediction);
                } else {
                    $session->set('prediction', 'Erreur : Aucune prédiction reçue.');
                }
            } catch (\Exception $e) {
                $session->set('prediction', 'Erreur lors de la communication avec l’API.');
            }

            return $this->redirectToRoute('app_result');
        }

        return $this->render('predict/form.html.twig', [
            'prediction' => $session->get('prediction', null), // Correction : on passe toujours la variable
        ]);
    }

    #[Route('/result', name: 'app_result', methods: ['GET'])]
    public function result(SessionInterface $session): Response
    {
        return $this->render('predict/form.html.twig', [
            'prediction' => $session->get('prediction', 'Aucune prédiction disponible.'),
        ]);
    }
}
