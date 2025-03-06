<?php

namespace App\Controller\GestionReclamation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\PredictType;

class PredictController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/prediction', name: 'app_prediction', methods: ['GET', 'POST'])]
    public function predict(Request $request, SessionInterface $session): Response
    {
        // Création du formulaire
        $form = $this->createForm(PredictType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $response = $this->httpClient->request('POST', 'http://localhost:5100/predict', [
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

        return $this->render('frontoffice/predict/form.html.twig', [
            'form' => $form->createView(),
            'prediction' => $session->get('prediction', null),
        ]);
    }

    #[Route('/result', name: 'app_result', methods: ['GET'])]
    public function result(SessionInterface $session): Response
    {
        return $this->render('frontoffice/predict/form.html.twig', [
            'form' => $this->createForm(PredictType::class)->createView(),
            'prediction' => $session->get('prediction', 'Aucune prédiction disponible.'),
        ]);
    }
}
