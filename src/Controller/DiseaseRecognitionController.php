<?php
namespace App\Controller;

use App\Service\DiseaseRecognitionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DiseaseRecognitionController extends AbstractController
{
    #[Route('/disease-recognition', name: 'disease_recognition', methods: ['GET', 'POST'])]
    public function index(Request $request, DiseaseRecognitionService $predictService, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $uploadedFile = $request->files->get('image');

            if ($uploadedFile) {
                // Assurez-vous que la méthode existe dans PredictService
                $prediction = $predictService->predictDisease($uploadedFile);

                // Stocker la prédiction en session
                $session->set('prediction', $prediction);

                return $this->redirectToRoute('disease_result');
            }
        }

        return $this->render('plant_disease/form.html.twig', [
            'prediction' => null,
        ]);
    }

    #[Route('/disease-result', name: 'disease_result', methods: ['GET'])]
    public function result(SessionInterface $session): Response
    {
        return $this->render('plant_disease/result.html.twig', [
            'prediction' => $session->get('prediction', 'Aucune prédiction disponible.'),
        ]);
    }
}
