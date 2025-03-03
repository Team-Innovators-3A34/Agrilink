<?php

namespace App\Controller\GestionRessource;

use App\Service\TwilioService;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Demandes;
use App\Entity\Notification;
use App\Form\DemandeType;
use App\Repository\RessourceRepository;
use App\Repository\DemandesRepository;
use App\Repository\RessourcesRepository; // Assurez-vous que ce chemin est correct
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;

final class DemandeController extends AbstractController
{
    private $emailJSService;
    private NotificationService $notificationService;
    private $ressourceRepo;
    private $security;

    public function __construct(RessourcesRepository $ressourceRepo,  NotificationService $notificationService, Security $security)
    {
        $this->ressourceRepo = $ressourceRepo;
        $this->notificationService = $notificationService;

        $this->security = $security;
    }

    #[Route('/demande', name: 'app_demande')]
    public function index(Request $request): Response
    {
        $demande = new Demandes();
        $form = $this->createForm(DemandeType::class, $demande);

        return $this->render('back/listResources.html.twig', [
            'form' => $form->createView(), // ✅ Assurez-vous de passer le formulaire ici
        ]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/showDemandes', name: 'demande_liste')]
    public function list(DemandesRepository $demandeRepository, Request $request): Response
    {


        $status = $request->query->get('status');
        if ($status) {
            $demandes = $demandeRepository->findByStatus($status);
        } else {
            $demandes = $demandeRepository->findAll();
        }

        return $this->render('demande/list.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/demande/{id}/proposition', name: 'ajouter_proposition', methods: ['POST'])]
    public function ajouterProposition(Request $request, Demandes $demande, EntityManagerInterface $entityManager): JsonResponse
    {
        $proposition = $request->request->get('proposition');

        if ($proposition) {
            $demande->setPropositon($proposition);
            $entityManager->persist($demande);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'proposition' => $proposition]);
        }

        return new JsonResponse(['success' => false]);
    }

    #[Route('/showDemande', name: 'demandes_liste')]
    public function list2(DemandesRepository $demandeRepository, EntityManagerInterface $entityManager): Response
    {
        $now = new \DateTime();

        // Récupérer les demandes en attente qui ont expiré
        $demandesExpirées = $entityManager->getRepository(Demandes::class)
            ->createQueryBuilder('d')
            ->where('d.status = :status')
            ->andWhere('d.expire_date <= :now')
            ->setParameter('status', 'terminé')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();



        foreach ($demandesExpirées as $demande) {
            $demande->setStatus('terminé');
            $entityManager->persist($demande);
            $entityManager->flush(); // Exécute la mise à jour immédiatement
        }

        $demandes = $demandeRepository->findAll();

        return $this->render('demandeshow.html.twig', [
            'demandes' => $demandes,
        ]);
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/profile/demander/{id}', name: 'demande_create')]
    public function createDemande(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        RessourcesRepository $ressourceRepo,
        MailerInterface $mailer,
        TwilioService $twilioService // Ajout du service Twilio
    ): Response {
        // Vérifier si la ressource existe
        $ressource = $ressourceRepo->find($id);
        if (!$ressource) {
            return $this->redirectToRoute('app_demande');
        }

        // Créer une nouvelle demande
        $demande = new Demandes();
        $demande->setRessourceId($ressource);
        $demande->setCreatedAt(new \DateTimeImmutable());
        $demande->setToUser($ressource->getUserId());
        $demande->setStatus('en cours');

        // Créer le formulaire
        $form = $this->createForm(DemandeType::class, $demande, [
            'required' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setUserId($this->getUser());

            $this->notificationService->demandeNotification($ressource->getUserId(), $this->getUser());

            $em->persist($demande);
            $em->flush();

            // ✉️ Envoi du SMS
            $message = sprintf(
                "Une nouvelle demande a été soumise pour la ressource : %s.",
                $ressource->getNameR()
            );
            $twilioService->sendSms('+21698476000', $message); // Remplace par le bon numéro

            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/demande/addDemande.html.twig', [
            'form' => $form->createView(),
            'ressource' => $ressource,
            "action" => "Ajouter"
        ]);
    }

    #[Route('/send', name: 'email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        // Crée un email
        $email = (new Email())
            ->from('abouzaouada@gmail.com')  // Ton adresse Gmail
            ->to('yasminebouzaouda@gmail.com')  // L'adresse du destinataire
            ->subject('Test Email from Symfony')
            ->text('This is a test email sent from Symfony using Gmail!')
            ->html('<p>This is a test email sent from Symfony using <strong>Gmail</strong>!</p>');

        // Envoie l'email
        $mailer->send($email);

        // Retourne une réponse
        return new Response('Email sent!');
    }

    #[Route('/demandes/{id}/update-status/{status}', name: 'app_demandes_update_status', methods: ['POST'])]
    public function updateStatus(Demandes $demande, string $status, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si la demande est expirée
        if ($demande->getExpireDate() <= new \DateTimeImmutable()) {
            $demande->setStatus('terminé');
        } else {
            $demande->setStatus($status);
        }

        $entityManager->persist($demande);
        $entityManager->flush();

        return $this->redirectToRoute('demandes_liste'); // Remplace avec ta route de liste des demandes
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/demandes/delete/{id}', name: 'app_demandes_delete', methods: ['GET', 'POST'])]
    public function delete(Demandes $demande, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($demande);
        $entityManager->flush();

        return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/profile/demandes/{id}/update', name: 'app_demandes_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Demandes $demande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);
        $ressource = $this->ressourceRepo->find($demande->getRessourceId());

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour de la demande
            $entityManager->flush();
            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/demande/addDemande.html.twig', [
            'form' => $form->createView(),
            'demande' => $demande,
            "action" => "Modifier",
            "ressource" => $ressource
        ]);
    }

    #[Route('timeline-data', name: 'demandes_timeline_data')]
    public function getTimelineData(DemandesRepository $demandeRepository): JsonResponse
    {
        $demandes = $demandeRepository->findAll();

        $data = [];

        foreach ($demandes as $demande) {
            $data[] = [
                'label' => $demande->getDemandeId(),
                'date' => $demande->getCreatedAt()->format('Y-m-d'),
                'status' => $demande->getStatus(),
            ];
        }

        return new JsonResponse($data);
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/demande/{id}/discussion', name: 'update_discussion', methods: ['POST'])]
    public function updateDiscussion(Request $request, Demandes $demande, EntityManagerInterface $entityManager): Response
    {
        $proposition = $request->request->get('proposition');
        $reponse = $request->request->get('reponse');

        if ($proposition !== null) {
            $demande->setProposition($proposition);
        }

        if ($reponse !== null) {
            $demande->setReponse($reponse);
        }

        $entityManager->persist($demande);
        $entityManager->flush();

        return $this->redirectToRoute('demande_liste');
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/demande/{id}/reponse1', name: 'save_response', methods: ['POST'])]
    public function saveResponse(Request $request, Demandes $demande, EntityManagerInterface $entityManage): Response
    {
        $reponse = $request->request->get('reponse');

        if ($reponse !== null) {
            $demande->setReponse($reponse);
        }

        $entityManage->persist($demande);
        $entityManage->flush();

        return $this->redirectToRoute('demande_liste');
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/acceptdemande/{id}', name: 'accept_demande', methods: ['GET', 'POST'])]
    public function acceptDemande(Demandes $demande, EntityManagerInterface $entityManage): Response
    {
        $demande->setStatus('accepté');
        $entityManage->persist($demande);
        $this->notificationService->acceptDemandeRessourceNotification($demande);
        $entityManage->flush();

        return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/refusedemande/{id}', name: 'refuse_demande', methods: ['GET', 'POST'])]
    public function refuseDemande(Demandes $demande, EntityManagerInterface $entityManage): Response
    {
        $demande->setStatus('refusé');
        $entityManage->persist($demande);
        $entityManage->flush();

        return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]

    #[Route('/api/mes-demandes-approuvees', name: 'api_mes_demandes_approuvees')]
    public function getUserApprovedDemandes(DemandesRepository $demandeRepository, Security $security): JsonResponse
    {
        // Récupère l'utilisateur connecté
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté'], 403);
        }

        // Récupérer uniquement les demandes approuvées du user connecté
        // Changer 'userId' par 'user' si c'est bien un objet User dans l'entité Demandes
        $demandes = $demandeRepository->findBy([
            'status' => 'accepté',
            'userId' => $user
        ]);

        $events = [];

        // Parcours chaque demande pour créer un événement
        foreach ($demandes as $demande) {
            // Assure-toi que expireDate et createdAt sont définis
            if ($demande->getExpireDate() && $demande->getCreatedAt()) {
                $events[] = [
                    'title' => $demande->getRessourceId()->getNom(), // Nom de la ressource
                    'start' => $demande->getCreatedAt()->format('Y-m-d'), // Date de création
                    'end' => $demande->getExpireDate()->format('Y-m-d'), // Date d'expiration
                    'color' => '#f39c12', // Couleur orange pour la période
                ];
            }
        }

        // Debug (supprimer quand ça fonctionne)
        dd($events);

        return new JsonResponse($events);
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]

    #[Route('/api/demandes/{id}', name: 'get_demandes', methods: ['GET'])]
    public function getDemandes(int $id, DemandesRepository $demandeRepo): JsonResponse
    {
        $demandes = $demandeRepo->findBy(['ressourceId' => $id]);

        $events = [];
        foreach ($demandes as $demande) {
            $events[] = [
                'title' => 'Déjà réservé',
                'start' => $demande->getCreatedAt()->format('Y-m-d'),
                'end' => $demande->getExpireDate()->format('Y-m-d'),
                'backgroundColor' => 'red',
                'borderColor' => 'red',
                'allDay' => true,
            ];
        }

        return $this->json($events);
    }

    #[Route('/test-mercure', name: 'test_mercure')]
    public function testMercure(HubInterface $hub): JsonResponse
    {
        try {
            $update = new Update(
                'https://example.com/books/1',
                json_encode(['message' => 'Test Mercure depuis Symfony']),
                true // Si ton hub Mercure exige un JWT
            );
            $hub->publish($update);

            return new JsonResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/api/demandes', name: 'api_demandes')]
    public function tDemandes(DemandesRepository $demandeRepository): JsonResponse
    {
        $demandes = $demandeRepository->findBy(['status' => 'accepté', 'userId' => $this->getUser()]);

        $events = [];
        foreach ($demandes as $demande) {
            $events[] = [
                'title' => $demande->getRessourceId()->getNameR(), // Adapter selon ton modèle
                'start' => $demande->getCreatedAt()->format('Y-m-d'), // Adapter selon ton modèle
                'end' => $demande->getExpireDate()->format('Y-m-d'), // Optionnel
            ];
        }

        return new JsonResponse($events);
    }
}
