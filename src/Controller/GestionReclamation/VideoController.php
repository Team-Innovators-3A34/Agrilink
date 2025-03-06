<?php
// src/Controller/VideoController.php
namespace App\Controller\GestionReclamation;

use App\Service\VideoGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    private VideoGeneratorService $videoGenerator;

    public function __construct(VideoGeneratorService $videoGenerator)
    {
        $this->videoGenerator = $videoGenerator;
    }

    
    #[Route('/video', name: 'video_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $result = null;

        if ($request->isMethod('POST')) {
            $description = $request->request->get('description');

            if (!empty($description)) {
                // Appel du service pour générer la vidéo
                $result = $this->videoGenerator->generateVideo($description);
            } else {
                $this->addFlash('error', 'Veuillez saisir une description.');
            }
        }

        return $this->render('video/index.html.twig', [
            'result' => $result,
        ]);
    }
}
