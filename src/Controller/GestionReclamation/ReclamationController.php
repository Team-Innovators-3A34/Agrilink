<?php

namespace App\Controller\GestionReclamation;

use App\Service\TranslationService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Reponses;

use App\Service\InfobipSmsSender;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;




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

    #[IsGranted('ROLE_ADMIN')]
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
    public function new(
        Request $request,
        ValidatorInterface $validator,
        HttpClientInterface $httpClient,
        InfobipSmsSender $smsSender
    ): Response {
        $reclamation = new Reclamation();
        $user = $this->getUser();

        // Définition des valeurs par défaut
        $reclamation->setIdUser($user);
        $reclamation->setNomUser($user->getNom() . " " . $user->getPrenom());
        $reclamation->setMailUser($user->getEmail());
        $reclamation->setStatus("En cours");
        $reclamation->setDate(new \DateTime());
        $reclamation->setPriorite(0);
        $reclamation->setArchive("non");

        // Création du formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        $wordsToMask = ['test', 'exemple']; // Ajoute d'autres mots si nécessaire

        // Validation explicite de l'entité
        $errors = $validator->validate($reclamation);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Remplacer le contenu de la réclamation par des '*'
                $content = $reclamation->getContent();
                $maskedContent = preg_replace_callback(
                    array_map(fn($word) => "/\b" . preg_quote($word, '/') . "\b/i", $wordsToMask),
                    fn($matches) => str_repeat('*', strlen($matches[0])),
                    $reclamation->getContent()
                );


                $reclamation->setContent($maskedContent); // Stocker le contenu masqué

                // Appel API Flask pour analyser l'état de la réclamation (positive/negative)
                $responseRec = $httpClient->request('POST', 'http://localhost:5001/predictetat_rec', [
                    'json' => ['text' => $content] // Utiliser le contenu original pour l'analyse
                ]);
                $dataRec = $responseRec->toArray();
                $etatRec = $dataRec['etat_rec'];
                $reclamation->setEtatRec($etatRec);

                // Appel API Flask pour analyser l'état émotionnel de l'utilisateur
                $responseUser = $httpClient->request('POST', 'http://localhost:5001/predictetat_user', [
                    'json' => ['texts' => [$content]] // Utiliser le contenu original pour l'analyse
                ]);
                $dataUser = $responseUser->toArray();
                $etatUser = $dataUser['predictions'][0];
                $reclamation->setEtatUser($etatUser);
            } catch (\Exception $e) {
                $this->addFlash('error', "Erreur lors de l'analyse de la réclamation : " . $e->getMessage());
                $reclamation->setEtatRec("inconnu");
                $reclamation->setEtatUser("inconnu");
            }

            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l’image.');
                }
                $reclamation->setImage($newFilename);
            }

            // Sauvegarde de la réclamation
            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            // Notification à l'administrateur (optionnel)
            $this->notificationService->claimNotification($user);

            /*
        // Envoi d'un SMS
        $phoneNumber = $user->getTelephone();
        if (!$phoneNumber) {
            $this->addFlash('error', "Aucun numéro de téléphone valide n'a été trouvé pour l'utilisateur.");
        } else {
            // Formatage du numéro : s'il ne commence pas par "216" ou "+216", on lui ajoute "216"
            $phoneNumber = trim($phoneNumber);
            if (!(str_starts_with($phoneNumber, '216') || str_starts_with($phoneNumber, '+216'))) {
                // Vous pouvez ajuster ici pour ajouter un "+" si nécessaire
                $phoneNumber = '216' . $phoneNumber;
            }

            $smsMessage = "Bonjour " . $user->getNom() . ", votre réclamation a bien été enregistrée.";
            try {
                $smsResponse = $smsSender->sendSms($phoneNumber, $smsMessage);
                // Pour déboguer, décommentez la ligne suivante :
                // dd($smsResponse);
            } catch (\Exception $e) {
                $this->addFlash('error', "Le SMS de notification n'a pas pu être envoyé : " . $e->getMessage());
            }
        }
        */

            $this->addFlash("success", "Votre réclamation a été enregistrée avec succès.");
            return $this->redirectToRoute('app_profilee', ['id' => $user->getId()]);
        }

        return $this->render('frontOffice/settings/claim/newClaim.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
            'action' => 'Ajouter'
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
                    $this->addFlash('error', 'Échec du téléversement de l\'image.');
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
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/dashboard/reclamationDetailsback/{id}', name: 'reclamationDetailsback', methods: ['GET'])]
    public function reclamationDetailsback(Request $request, $id, TranslationService $translator): Response
    {
        // Récupération de la réclamation depuis le repository
        $reclamation = $this->reclamationRepository->find($id);
        if (!$reclamation) {
            return $this->redirectToRoute('app_reclamationback_list');
        }

        // Récupération du paramètre de langue avec valeur par défaut vide (affichage en base)
        $targetLanguage = $request->query->get('lang', '');
        // La traduction est effectuée seulement si une langue cible est précisée
        $useTranslation = ($targetLanguage !== '');

        $translatedReclamation = [
            'nomUser'  => $reclamation->getNomUser(),
            'mailUser' => $reclamation->getMailUser(),
            'title'    => $useTranslation
                ? ($translator->translateText($reclamation->getTitle(), $targetLanguage) ?: $reclamation->getTitle())
                : $reclamation->getTitle(),
            'content'  => $useTranslation
                ? ($translator->translateText($reclamation->getContent(), $targetLanguage) ?: $reclamation->getContent())
                : $reclamation->getContent(),
            'status'   => $useTranslation
                ? ($translator->translateText($reclamation->getStatus(), $targetLanguage) ?: $reclamation->getStatus())
                : $reclamation->getStatus(),
            'date'     => $reclamation->getDate(),
            'type'     => $reclamation->getType()
                ? ($useTranslation
                    ? ($translator->translateText($reclamation->getType()->getNom(), $targetLanguage) ?: $reclamation->getType()->getNom())
                    : $reclamation->getType()->getNom())
                : null,
            'priorite' => $useTranslation
                ? ($translator->translateText((string)$reclamation->getPriorite(), $targetLanguage) ?: $reclamation->getPriorite())
                : $reclamation->getPriorite(),
            'image'    => $reclamation->getImage(),
        ];

        // Traduction des réponses associées
        $translatedResponses = [];
        if (method_exists($reclamation, 'getReponses')) {
            foreach ($reclamation->getReponses() as $response) {
                $translatedResponses[] = [
                    'content'  => $useTranslation
                        ? ($translator->translateText($response->getContent(), $targetLanguage) ?: $response->getContent())
                        : $response->getContent(),
                    'solution' => $useTranslation
                        ? ($translator->translateText($response->getSolution(), $targetLanguage) ?: $response->getSolution())
                        : $response->getSolution(),
                    'status'   => $useTranslation
                        ? ($translator->translateText($response->getStatus(), $targetLanguage) ?: $response->getStatus())
                        : $response->getStatus(),
                    'date'     => $response->getDate(),
                    'isAuto'   => $response->isAuto(),
                ];
            }
        }

        return $this->render('backOffice/reclamations/detailReclamation.html.twig', [
            'reclamationId'         => $reclamation->getId(),
            'translatedReclamation' => $translatedReclamation,
            'translatedResponses'   => $translatedResponses,
            'targetLanguage'        => $targetLanguage,
        ]);
    }




    //////////////////////////////   CHANGER STATUS RECLMATION //////////////////////////////////////////////////////////
    #[Route('/reclamation/update/{id}/{status}', name: 'reclamation_update_status', methods: ['POST'])]
    public function updateStatus($id, string $status, EntityManagerInterface $em, Request $request): Response
    {
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            $this->addFlash('error', 'Réclamation introuvable.');
            return $this->redirectToRoute('app_reclamation_list_dashboard');
        }

        // Vérification du token CSRF pour la sécurité
        if (!$this->isCsrfTokenValid('update_status' . $reclamation->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_reclamation_list_dashboard');
        }

        // Mettre à jour le statut
        $reclamation->setStatus($status);
        $em->persist($reclamation);
        $em->flush();

        $this->addFlash('success', 'Le statut de la réclamation a été mis à jour avec succès.');

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
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/dashboard/reclamation/{id}/reponses', name: 'reclamation_reponses')]
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
        $html = $this->renderView('backOffice/reclamations/pdf_template.html.twig', [
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



    ////////////////////////   AFFICHAGE DES RECLAMATION ARCHIVE  //////////////////////////////////
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/dashboard/listReclamationArchive', name: 'app_reclamation_list-archive_dashboard', methods: ['GET'])]
    public function listReclamationArchive(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();
        return $this->render('backOffice/reclamations/listReclamationArchive.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }






    ////////////////////////  ARCHIVER     //////////////////////////

    #[Route('/reclamation/{id}/archiver', name: 'reclamation_archiver', methods: ['POST'])]
    public function archiver(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('archive' . $reclamation->getId(), $request->request->get('_token'))) {
            $reclamation->setArchive('oui');
            $entityManager->persist($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_list_dashboard'); // Remplace par le bon nom de ta route
    }



    ///////////////////////////////////   STAT DASHBOARD    ///////////////////////////////////////////////
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function adminDashboard(ReclamationRepository $reclamationRepo): Response
    {
        // Statistiques principales
        $stats = [
            'total' => $reclamationRepo->count([]),
            'resolved' => $reclamationRepo->count(['status' => 'acceptée']),
            'pending' => $reclamationRepo->count(['status' => 'En cours'])
        ];

        // Statistiques mensuelles formatées
        $monthlyStats = $reclamationRepo->createQueryBuilder('r')
            ->select("r.date as date, COUNT(r.id) as count")
            ->groupBy('r.date')
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();

        // Formater les résultats
        $formattedStats = [];
        foreach ($monthlyStats as $stat) {
            $month = $stat['date']->format('Y-m'); // Formatez ici
            $formattedStats[] = ['month' => $month, 'count' => $stat['count']];
        }

        return $this->render('backoffice/dashboard/dashboard.html.twig', [
            'stats' => $stats,
            'monthlyStats' => $formattedStats
        ]);
    }
}
