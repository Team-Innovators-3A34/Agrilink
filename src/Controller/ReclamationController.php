<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Reponses;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Form\ReponsesType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


use Knp\Snappy\Pdf;




final class ReclamationController extends AbstractController
{
    private $reclamationRepository;
    private $entityManager;

    public function __construct(ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager)
    {
        $this->reclamationRepository = $reclamationRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

    //////////////////////////////////   AFFICHER   ////////////////////////////////////////////////////////////

    #[Route('/reclamation/afficher', name: 'app_reclamation_list', methods: ['GET'])]
    public function listReclamation(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();
        return $this->render('reclamation/afficher.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }



    //////////////////////////////////   AFFICHER Back  ////////////////////////////////////////////////////////////

    #[Route('/reclamation/afficherb', name: 'app_reclamationback_list', methods: ['GET'])]
    public function listReclamationback(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $searchTerm = $request->query->get('search'); // Récupérer le terme de recherche
        $sortBy = $request->query->get('sortBy', 'date_desc'); // Critère de tri par défaut

        // Créer un query builder pour récupérer les réclamations triées
        $queryBuilder = $reclamationRepository->createQueryBuilder('r');

        // Appliquer le tri en fonction du paramètre 'sortBy'
        switch ($sortBy) {
            case 'date_asc':
                $queryBuilder->orderBy('r.date', 'ASC');
                break;
            case 'date_desc':
            default:
                $queryBuilder->orderBy('r.date', 'DESC');
                break;
            case 'status_asc':
                $queryBuilder->orderBy('r.status', 'ASC');
                break;
            case 'status_desc':
                $queryBuilder->orderBy('r.status', 'DESC');
                break;
        }

        // Si un terme de recherche est fourni, appliquer le filtre
        if ($searchTerm) {
            $queryBuilder
                ->andWhere('r.title LIKE :searchTerm OR r.content LIKE :searchTerm OR r.nom_user LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Récupérer les réclamations triées et filtrées
        $reclamations = $queryBuilder->getQuery()->getResult();

        // Statistiques des réclamations répondus et non répondus
        $reclamationsRepondus = $reclamationRepository->count(['status' => 'acceptée']);
        $reclamationsNonRepondus = $reclamationRepository->count(['status' => ['En cours', 'rejetée']]);

        return $this->render('reclamation/afficherb.html.twig', [
            'reclamations' => $reclamations,
            'search' => $searchTerm, // Pour afficher le terme de recherche dans l'input
            'reclamationsRepondus' => $reclamationsRepondus,
            'reclamationsNonRepondus' => $reclamationsNonRepondus,
            'sortBy' => $sortBy, // Passer le critère de tri à la vue
        ]);
    }

//////////////////////////////////   AJOUTER   ////////////////////////////////////////////////////////////

#[Route('/reclamation/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, ValidatorInterface $validator, HttpClientInterface $httpClient): Response
{
    $reclamation = new Reclamation();
    $reclamation->setIdUser(0);
    $reclamation->setStatus("En cours");
    $reclamation->setPriorite(0);

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    $errors = $validator->validate($reclamation);

    if ($form->isSubmitted() && $form->isValid()) {
        try {
            // 🔹 Appel API Flask pour analyser l'état de la réclamation (positive/negative)
            $responseRec = $httpClient->request('POST', 'http://localhost:8001/predictetat_rec', [
                'json' => ['text' => $reclamation->getContent()]
            ]);

            $dataRec = $responseRec->toArray();
            $etatRec = $dataRec['etat_rec']; // "positive" ou "negative"
            $reclamation->setEtatRec($etatRec);

            // 🔹 Appel API Flask pour analyser l'état émotionnel de l'utilisateur
            $responseUser = $httpClient->request('POST', 'http://localhost:8001/predictetat_user', [
                'json' => ['texts' => [$reclamation->getContent()]]
            ]);

            $dataUser = $responseUser->toArray();
            $etatUser = $dataUser['predictions'][0]; // Emotion: "Sadness", "Joy", etc.
            $reclamation->setEtatUser($etatUser);

        } catch (\Exception $e) {
            $this->addFlash('error', "Erreur lors de l'analyse de la réclamation : " . $e->getMessage());
            $etatRec = "inconnu"; // Valeur par défaut si l'API ne fonctionne pas
            $etatUser = "inconnu"; // Valeur par défaut si l'API ne fonctionne pas
            $reclamation->setEtatRec($etatRec);
            $reclamation->setEtatUser($etatUser);
        }

        // 🔹 Gestion de l'image
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('images_directory'), $newFilename);
            $reclamation->setImage($newFilename);
        }

        // 🔹 Enregistrement de la réclamation
        $this->entityManager->persist($reclamation);
        $this->entityManager->flush();

        $this->addFlash('success', 'Réclamation enregistrée avec succès.');
        return $this->redirectToRoute('app_reclamation_list');
    }

    return $this->render('reclamation/new.html.twig', [
        'form' => $form->createView(),
        'errors' => $errors
    ]);
}




    //////////////////////////////////   UPDATE   ////////////////////////////////////////////////////////////

    #[Route('/reclamation/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Reclamation $reclamation): Response
{
    // Créer le formulaire
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'image
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            // Récupérer le nom du fichier original
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

            // Remplacer les caractères non-alphanumériques par des underscores
            $safeFilename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer l'exception si nécessaire
            }

            // Enregistrer la nouvelle image dans l'entité
            $reclamation->setImage($newFilename);
        }

        // Sauvegarder les changements dans la base de données
        $this->entityManager->flush();

        return $this->redirectToRoute('app_reclamation_list'); // Rediriger après la modification
    }

    return $this->render('reclamation/edit.html.twig', [
        'form' => $form->createView(),
        'reclamation' => $reclamation,
    ]);
}


    //////////////////////////////////   DELETE   ////////////////////////////////////////////////////////////

    #[Route('/reclamation/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($reclamation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_list');
    }


    //////////////////////////////////   DELETE BACK  ////////////////////////////////////////////////////////////

    #[Route('/reclamationback/{id}', name: 'app_reclamation_deleteback', methods: ['POST'])]
    public function deleteback(Request $request, Reclamation $reclamation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($reclamation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamationback_list');
    }

    //////////////////////////////////   SHOW RECLAMATION   ////////////////////////////////////////////////////////////

    #[Route('/reclamationDetails/{id}', name: 'reclamationDetails', methods: ['GET'])]
    public function reclamationDetails($id): Response
    {
        $reclamation = $this->reclamationRepository->find($id);

        if (!$reclamation) {
            return $this->redirectToRoute('app_reclamation_list');  // Redirect if reclamation not found
        }

        return $this->render('reclamation/showreclamation.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }


    //////////////////////////////////   SHOW RECLAMATION BACK  ////////////////////////////////////////////////////////////

    // Route de détails pour le back-end
#[Route('/reclamationDetailsback/{id}', name: 'reclamationDetailsback', methods: ['GET'])]
public function reclamationDetailsback($id): Response
{
    $reclamation = $this->reclamationRepository->find($id);

    if (!$reclamation) {
        return $this->redirectToRoute('app_reclamationback_list');  // Redirect if reclamation not found
    }

    return $this->render('reclamation/showb.html.twig', [  // Assurez-vous que la vue est la bonne
        'reclamation' => $reclamation,
    ]);
}




//////////////////////////////   CHANGER STATUS RECLMATION //////////////////////////////////////////////////////////
#[Route('/reclamation/update/{id}/{status}', name: 'reclamation_update_status', methods: ['POST'])]
public function updateStatus($id, string $status, EntityManagerInterface $em, Request $request): Response
{
    $reclamation = $em->getRepository(Reclamation::class)->find($id);

    if (!$reclamation) {
        $this->addFlash('error', 'Réclamation introuvable.');
        return $this->redirectToRoute('app_reclamationback_list');
    }

    // Vérification du token CSRF pour la sécurité
    if (!$this->isCsrfTokenValid('update_status' . $reclamation->getId(), $request->request->get('_token'))) {
        $this->addFlash('error', 'Action non autorisée.');
        return $this->redirectToRoute('app_reclamationback_list');
    }

    // Mettre à jour le statut
    $reclamation->setStatus($status);
    $em->persist($reclamation);
    $em->flush();

    $this->addFlash('success', 'Le statut de la réclamation a été mis à jour avec succès.');

    return $this->redirectToRoute('reclamationDetailsback', ['id' => $id]);
}








///////////////////////////////////////// AJOUTER UN LIVRE POUR L'AUTEUR ///////////////////////////////////////////////



#[Route('/reclamation/{id}/addresponse', name: 'reclamation_response_add')]
public function addauthorBooks(int $id, Request $request): Response
{
    // Récupérer l'auteur à partir de l'ID
    $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id);

    if (!$reclamation) {
        throw $this->createNotFoundException('Auteur non trouvé.');
    }

    // Créer un nouveau livre
    $reponse = new Reponses();
    $reponse->setPublished(true);

    // Créer le formulaire pour ajouter un livre en excluant le champ 'author'
    $form = $this->createForm(ReponsesType::class, $reponse, [
        'exclude_author' => true, // L'auteur ne sera pas affiché dans le formulaire
    ]);
    
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Associer l'auteur récupéré au livre
        $reponse->setReclamation($reclamation);

       

        // Sauvegarder le livre et mettre à jour l'auteur dans la base de données
        $this->entityManager->persist($reponse);
        $this->entityManager->flush();

        // Rediriger vers la page des détails de l'auteur ou liste des auteurs après ajout
        return $this->redirectToRoute('reclamationDetailsback', ['id' => $reclamation->getId()], Response::HTTP_SEE_OTHER);
    }

    // Rendre la vue pour afficher le formulaire d'ajout de livre
    return $this->render('reponses/add.html.twig', [
        'form' => $form->createView(),
        
    ]);
}



/////////////////////////////////////////  AFFICHAGE DES REPONSES DE RECLAMATION BACK  ///////////////////////////////////

#[Route('/reclamation/{id}/reponses', name: 'reclamation_reponses')]
public function reclamationReponses(int $id): Response
{
    // Récupérer la réclamation via son ID
    $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id);

    // Vérifier si la réclamation existe
    if (!$reclamation) {
        $this->addFlash('error', 'Réclamation non trouvée.');
        return $this->redirectToRoute('app_reclamationback_list');
    }

    // Récupérer les réponses associées à cette réclamation
    $reponses = $reclamation->getReponses();

    // Passer la réclamation et ses réponses à la vue
    return $this->render('reclamation/reponses.html.twig', [
        'reclamation' => $reclamation,
        'reponses' => $reponses,
    ]);
}





/////////////////////////////////////////  AFFICHAGE DES REPONSES DE RECLAMATION FRONT  ///////////////////////////////////

#[Route('/reclamationfront/{id}/reponses', name: 'reclamationfront_reponses')]
public function reclamationReponsesfront(int $id): Response
{
    // Récupérer la réclamation via son ID
    $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id);

    // Vérifier si la réclamation existe
    if (!$reclamation) {
        $this->addFlash('error', 'Réclamation non trouvée.');
        return $this->redirectToRoute('app_reclamation_list');
    }

    // Récupérer les réponses associées à cette réclamation
    $reponses = $reclamation->getReponses();

    // Passer la réclamation et ses réponses à la vue
    return $this->render('reclamation/reponsesfront.html.twig', [
        'reclamation' => $reclamation,
        'reponses' => $reponses,
    ]);
}

/////////////////////////////////////////  PDF  ////////////////////////////////#[Route('/reclamation/pdf/{id}', name: 'reclamation_pdf')]
#[Route('/reclamation/pdf/{id}', name: 'reclamation_pdf')]
public function generatePdf($id, EntityManagerInterface $em, Pdf $snappy): Response
{
    // Récupérer la réclamation depuis la base de données
    $reclamation = $em->getRepository(Reclamation::class)->find($id);

    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    // Générer la vue HTML
    $html = $this->renderView('reclamation/pdf_template.html.twig', [
        'reclamation' => $reclamation,
    ]);

    // Convertir en PDF
    $pdf = $snappy->getOutputFromHtml($html);

    // Retourner le PDF en réponse
    return new Response($pdf, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="reclamation_' . $id . '.pdf"',
    ]);
}


}
