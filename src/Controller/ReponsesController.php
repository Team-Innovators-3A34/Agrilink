<?php

// src/Controller/ReponsesController.php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponses;
use App\Form\ReponsesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Contracts\HttpClient\HttpClientInterface;






use App\Repository\ReponsesRepository;


class ReponsesController extends AbstractController
{
    private $reponsesRepository;
    private $entityManager;
    private $rapidApiKey;
    private $httpClient;

      public function __construct(ReponsesRepository $reponsesRepository, EntityManagerInterface $entityManager, HttpClientInterface $httpClient, string $rapidApiKey)
    {
        $this->reponsesRepository = $reponsesRepository;
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->rapidApiKey = $rapidApiKey;
    }



/////////////////////////////////////////////// AJOUTER REPONSES /////////////////////////////////////////////////////////////

#[Route('/reclamation/{id}/repondre', name: 'reclamation_repondre', methods: ['GET', 'POST'])]
    public function repondre(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée.');
        }

        $reponse = new Reponses();
        $reponse->setReclamation($reclamation);

        $form = $this->createForm(ReponsesType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isAuto = $form->get('isAuto')->getData();

            if ($isAuto) {
                // Générer une réponse automatique
                $generatedResponse = $this->generateAIResponse($reclamation->getContent());

                if ($generatedResponse) {
                    $reponse->setContent($generatedResponse);
                    $reponse->setDate(new \DateTime());
                } else {
                    $this->addFlash('danger', 'Erreur lors de la génération de la réponse automatique.');
                    return $this->redirectToRoute('reclamation_repondre', ['id' => $reclamation->getId()]);
                }
            } else {
                // Si isAuto n'est pas coché, le champ content ne doit pas être vide
                if (!$reponse->getContent()) {
                    $this->addFlash('danger', 'Veuillez entrer une réponse.');
                    return $this->redirectToRoute('reclamation_repondre', ['id' => $reclamation->getId()]);
                }

                if (!$reponse->getDate()) {
                    $reponse->setDate(new \DateTime());
                }
            }

            $entityManager->persist($reponse);
            $entityManager->flush();

            $this->addFlash('success', 'Réponse envoyée avec succès.');
            return $this->redirectToRoute('app_reclamationback_list');
        }

        return $this->render('reponses/repondre.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
            'generatedResponse' => $reponse->getContent() // Utiliser la réponse générée
        ]);
    }

    private function generateAIResponse(string $reclamationContent): ?string
    {
        try {
            $apiResponse = $this->httpClient->request('POST', 'https://chatgpt-42.p.rapidapi.com/chatgpt', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-rapidapi-key' => $this->rapidApiKey,
                    'x-rapidapi-host' => 'chatgpt-42.p.rapidapi.com',
                ],
                'json' => [
                    'messages' => [
                        ['role' => 'user', 'content' => $reclamationContent]
                    ],
                    'web_access' => false,
                ],
            ]);

            $content = $apiResponse->toArray();

            if (isset($content['result']) && !empty($content['result'])) {
                return $content['result'];
            } else {
                $this->addFlash('danger', 'Aucune réponse générée.');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur API : ' . $e->getMessage());
        }

        return null;
    }




 
/////////////////////////////////  Detail BACK //////////////////////////////////////
#[Route('"/reponse/{id}/detail', name: 'reponse_detail', methods: ['GET'])]
public function detail($id):Response
{
    $reponses = $this->reponsesRepository->find($id);
    if (!$reponses) {
        throw $this->createNotFoundException('La réponse n\'existe pas');
    }

    return $this->render('reponses/detail.html.twig', [
        'reponse' => $reponses,
    ]);
}




/////////////////////////////////  Detail FRONT //////////////////////////////////////
#[Route('"/reponsefront/{id}/detail', name: 'reponsefront_detail', methods: ['GET'])]
public function detailfront($id):Response
{
    $reponses = $this->reponsesRepository->find($id);
    if (!$reponses) {
        throw $this->createNotFoundException('La réponse n\'existe pas');
    }

    return $this->render('reponses/detailfront.html.twig', [
        'reponse' => $reponses,
    ]);
}





///////////////////////////////// SUPPRIMER BACK ////////////////////////////////////////

#[Route('/reponse/{id}/delete', name: 'reponse_delete', methods: ['POST'])]
public function delete(Request $request, Reponses $reponses): Response
{
    
        $this->entityManager->remove($reponses);
        $this->entityManager->flush();  // Valider la suppression en base de données
    

    // Rediriger vers la page de gestion des réponses
    return $this->redirectToRoute('reclamation_reponses', ['id' => $reponses->getReclamation()->getId()]);
}






 //////////////////////////////////   UPDATE  BACK ////////////////////////////////////////////////////////////
 #[Route('/reponses/{id}/edit', name: 'app_reponses_edit', methods: ['GET', 'POST'])]
 public function edit(Request $request, Reponses $reponses, EntityManagerInterface $entityManager): Response
 {
     // Vérifier si la réponse est associée à une réclamation
     $reclamation = $reponses->getReclamation();
     if (!$reclamation) {
         throw $this->createNotFoundException('La réclamation associée à cette réponse n\'existe pas.');
     }
 
     // Créer le formulaire
     $form = $this->createForm(ReponsesType::class, $reponses);
     $form->handleRequest($request);
 
     // Vérifier si le formulaire est soumis et valide
     if ($form->isSubmitted() && $form->isValid()) {
         // Enregistrer les modifications en base de données
         $entityManager->flush();
 
         // Rediriger vers la page des réponses de la réclamation après l'édition
         return $this->redirectToRoute('reclamation_reponses', ['id' => $reclamation->getId()]);
     }
 
     // Rendu du formulaire d'édition
     return $this->render('reponses/edit.html.twig', [
         'form' => $form->createView(),
         'reponses' => $reponses, 
         'reclamation' => $reclamation, // Passer la réclamation à la vue
     ]);
 }
 









}
