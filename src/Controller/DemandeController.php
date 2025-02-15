<?php

namespace App\Controller;
use App\Service\EmailJSService;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Demandes;
use App\Form\DemandeType;
use App\Repository\RessourceRepository;
use App\Repository\RessourcesRepository; // Assurez-vous que ce chemin est correct
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DemandesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class DemandeController extends AbstractController
{
    private $emailJSService;
    private NotificationService $notificationService;
    private $ressourceRepo;

    public function __construct(RessourcesRepository $ressourceRepo ,EmailJSService $emailJSService, NotificationService $notificationService)
    {
        $this->ressourceRepo = $ressourceRepo;
        $this->emailJSService = $emailJSService;
        $this->notificationService = $notificationService;
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
    #[Route('/showDemandes', name: 'demande_liste')]
    public function list(DemandesRepository $demandeRepository,Request $request): Response
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

    // Récupérer les demandes en cours qui ont expiré
    $demandesExpirées = $entityManager->getRepository(Demandes::class)
        ->createQueryBuilder('d')
        ->where('d.status = :status')
        ->andWhere('d.expire_date <= :now')
        ->setParameter('status', 'en cours')
        ->setParameter('now', $now)
        ->getQuery()
        ->getResult();

    // Mettre à jour le statut des demandes expirées
    if (!empty($demandesExpirées)) {
        foreach ($demandesExpirées as $demande) {
            $demande->setStatus('terminé');
            $entityManager->persist($demande);
        }
        $entityManager->flush();
    }

    // Récupérer toutes les demandes après mise à jour
    $demandes = $demandeRepository->findAll();

    return $this->render('demandeshow.html.twig', [
        'demandes' => $demandes,
    ]);
}

    #[Route('/demander/{id}', name: 'demande_create')]
public function createDemande(int $id, Request $request, EntityManagerInterface $em, RessourcesRepository $ressourceRepo,MailerInterface $mailer ): Response
{
    // Vérifier si la ressource existe
    $ressource = $ressourceRepo->find($id);
    if (!$ressource) {
        $this->addFlash('error', 'Ressource non trouvée.');
        return $this->redirectToRoute('app_demande'); 
    }

    // Créer une nouvelle demande
    $demande = new Demandes();
    $demande->setIdR($id);
    $demande->setCreatedAt(new \DateTimeImmutable());
  
    $demande->setStatus('en cours');

    // Créer le formulaire
    $form = $this->createForm(DemandeType::class, $demande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      
         // Crée un email
        $email = (new Email())
        ->from('abouzaouada@gmail.com')  // Ton adresse Gmail
        ->to('yasminebouzaouda@gmail.com')  // L'adresse du destinataire
        ->subject('Test Email from Symfony')
        ->text('This is a test email sent from Symfony using Gmail!')
        ->html('<p>This is a test email sent from Symfony using <strong>Gmail</strong>!</p>');

    // Envoie l'email
    $mailer->send($email);

    
        $em->persist($demande);
        $em->flush();
       
        $this->addFlash('success', 'Demande envoyée avec succès!');
        return $this->redirectToRoute('demandes_liste'); 
    }

    return $this->render('demande/create.html.twig', [
        'form' => $form->createView(), 
        'ressource' => $ressource
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

    $this->addFlash('success', 'Le statut a été mis à jour avec succès.');
    return $this->redirectToRoute('demandes_liste'); // Remplace avec ta route de liste des demandes
}
#[Route('/demandes/delete/{id}', name: 'app_demandes_delete', methods: ['POST'])]
public function delete(Demandes $demande, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($demande);
    $entityManager->flush();

    return $this->redirectToRoute('demandes_liste');
}
#[Route('/demandes/{id}/update', name: 'app_demandes_update', methods: ['GET', 'POST'])]
public function update(Request $request, Demandes $demande, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(DemandeType::class, $demande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       
        $entityManager->flush();
        $this->addFlash('success', 'Demande mise à jour avec succès.');
        return $this->redirectToRoute('demandes_liste');
    }

    return $this->render('demande/update.html.twig', [
        'form' => $form->createView(),
        'demande' => $demande,
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

#[Route('/demande/{id}/reponse1', name: 'save_response', methods: ['POST'])]
public function saveResponse(Request $request, Demandes $demande, EntityManagerInterface $entityManage ):Response
  {  $reponse = $request->request->get('reponse');
  
   
   

    if ($reponse !== null) {
        $demande->setReponse($reponse);
    }

    $entityManager->persist($demande);
    $entityManager->flush();

    return $this->redirectToRoute('demande_liste');
}

    
}

