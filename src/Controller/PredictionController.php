<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PredictionController extends AbstractController
{
    #[Route('/predict', name: 'predict_crop', methods: ['GET', 'POST'])]
    public function predict(Request $request): Response
    {/*
        $data = json_decode($request->getContent(), true);

        // Envoyer les données à l'API Flask
        $client = HttpClient::create();
        $response = $client->request('POST', 'http://127.0.0.1:5001/predict', [
            'json' => $data
        ]);

        // Récupérer la réponse de Flask
        $prediction = $response->toArray();

        return new JsonResponse($prediction);*/
        return $this->render('frontoffice/features/cropRecommendation.html.twig', []);
    }
}
