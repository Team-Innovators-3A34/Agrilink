<?php

namespace App\Controller\GestionReclamation;

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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Service\NotificationService;

use Knp\Snappy\Pdf;




final class ReclamationController extends AbstractController
{
    private $reclamationRepository;
    private $entityManager;
    private $notificationService;

    public function __construct(ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager, NotificationService $notificationService)
    {
        $this->reclamationRepository = $reclamationRepository;
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
    }

    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

    //////////////////////////////////   AFFICHER   ////////////////////////////////////////////////////////////

    /*#[Route('/reclamation/afficher', name: 'app_reclamation_list', methods: ['GET'])]
    public function listReclamation(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();
        return $this->render('reclamation/afficher.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }*/

    #[Route('/dashboard/listReclamation', name: 'app_reclamation_list_dashboard', methods: ['GET'])]
    public function listReclamation(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();
        return $this->render('backOffice/reclamations/listReclamation.html.twig', [
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

    #[Route('/settings/Claim', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $reclamation = new Reclamation();

        $user = $this->getUser();
        // Définir la valeur par défaut de id_user à 0
        $reclamation->setIdUser($user);
        $reclamation->setNomUser($user->getNom() . " " . $user->getPrenom());
        $reclamation->setMailUser($user->getEmail());
        $reclamation->setStatus("En cours");
        $reclamation->setDate(new \DateTime());
        $reclamation->setPriorite(0);

        // Créer le formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        // Validation explicite de l'entité avant de la persister
        $errors = $validator->validate($reclamation);

        if ($form->isSubmitted()) {
            // Si des erreurs de validation existent
            if (count($errors) > 0) {
                return $this->render('frontOffice/settings/claim/newClaim.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                    "action" => "Ajouter"  // Passer les erreurs à la vue
                ]);
            }

            // Si le formulaire est valide
            if ($form->isValid()) {
                // Gestion de l'image
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
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

                    // Enregistrer l'image dans l'entité
                    $reclamation->setImage($newFilename);
                }

                // Sauvegarder la réclamation dans la base de données
                $this->entityManager->persist($reclamation);
                $this->entityManager->flush();

                // Envoyer une notification à l'administrateur
                $this->notificationService->claimNotification($user);



                return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
            }
        }

        return $this->render('frontOffice/settings/claim/newClaim.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,  // Passer les erreurs à la vue, même si elles sont vides
            "action" => 'Ajouter'
        ]);
    }


    //////////////////////////////////   UPDATE   ////////////////////////////////////////////////////////////

    #[Route('/settings/Claim/{id}', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation, [
            'attr' => ['novalidate' => 'novalidate']
        ]);

        $form->handleRequest($request);

        // Récupération de l'image actuelle avant modification
        $oldImage = $reclamation->getImage();

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();

            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),  // Répertoire défini dans parameters.yml
                        $filename
                    );

                    // Supprimer l'ancienne image si elle existe
                    if ($oldImage) {
                        $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    // Mettre à jour l'image avec le nouveau fichier
                    $reclamation->setImage($filename);
                } catch (\Exception $e) {
                    // Gérer l'exception en cas d'erreur
                }
            } else {
                // Conserver l'ancienne image si aucune nouvelle n'est téléversée
                $reclamation->setImage($oldImage);
            }

            // Sauvegarde en base de données
            $entityManager->flush();


            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontOffice/settings/claim/newClaim.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
            'action' => 'Modifier'
        ]);
    }








    //////////////////////////////////   DELETE   ////////////////////////////////////////////////////////////

    #[Route('/reclamation/{id}', name: 'app_reclamation_delete', methods: ['POST', 'GET'])]
    public function delete(Reclamation $reclamation): Response
    {
        $this->entityManager->remove($reclamation);
        $this->entityManager->flush();


        return $this->redirectToRoute('app_reclamation_list_dashboard');
    }


    //////////////////////////////////   DELETE BACK  ////////////////////////////////////////////////////////////

    #[Route('/reclamationback/{id}', name: 'app_reclamation_deleteback', methods: ['POST', 'GET'])]
    public function deleteback(Request $request, Reclamation $reclamation): Response
    {

        $this->entityManager->remove($reclamation);
        $this->entityManager->flush();


        return $this->redirect($request->headers->get('referer'));
    }

    //////////////////////////////////   SHOW RECLAMATION   ////////////////////////////////////////////////////////////

    #[Route('/reclamationDetails/{id}', name: 'reclamationDetails', methods: ['GET'])]
    public function reclamationDetails($id): Response
    {
        $reclamation = $this->reclamationRepository->find($id);

        if (!$reclamation) {
            return $this->redirectToRoute('app_reclamation_list');  // Redirect if reclamation not found
        }

        return $this->render('frontoffice/settings/claim/detailClaim.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }


    //////////////////////////////////   SHOW RECLAMATION BACK  ////////////////////////////////////////////////////////////

    // Route de détails pour le back-end
    #[Route('/dashboard/reclamationDetailsback/{id}', name: 'reclamationDetailsback', methods: ['GET'])]
    public function reclamationDetailsback($id): Response
    {
        $reclamation = $this->reclamationRepository->find($id);

        if (!$reclamation) {
            return $this->redirectToRoute('app_reclamationback_list');  // Redirect if reclamation not found
        }

        return $this->render('backOffice/reclamations/detailReclamation.html.twig', [  // Assurez-vous que la vue est la bonne
            'reclamation' => $reclamation,
        ]);
    }




    //////////////////////////////   CHANGER STATUS RECLMATION //////////////////////////////////////////////////////////
    #[Route('/reclamation/update/{id}/{status}', name: 'reclamation_update_status', methods: ['POST'])]
    public function updateStatus($id, string $status, EntityManagerInterface $em, Request $request): Response
    {
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            return $this->redirectToRoute('app_reclamation_list_dashboard');
        }

        // Vérification du token CSRF pour la sécurité
        if (!$this->isCsrfTokenValid('update_status' . $reclamation->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_reclamation_list_dashboard');
        }

        // Mettre à jour le statut
        $reclamation->setStatus($status);
        $em->persist($reclamation);
        $em->flush();


        return $this->redirectToRoute('app_reclamation_list_dashboard');
    }








    ///////////////////////////////////////// AJOUTER UN reponse POUR reclamation ///////////////////////////////////////////////



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

    #[Route('/dashboard/reclamation/{id}/reponses', name: 'reclamation_reponses')]
    public function reclamationReponses(int $id): Response
    {
        // Récupérer la réclamation via son ID
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            return $this->redirectToRoute('app_reclamationback_list');
        }

        // Récupérer les réponses associées à cette réclamation
        $reponses = $reclamation->getReponses();

        // Passer la réclamation et ses réponses à la vue
        return $this->render('backOffice/reclamations/listReponse.html.twig', [
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
