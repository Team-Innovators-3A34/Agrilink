<?php

namespace App\Controller;

use App\Entity\Event; // Entité pour stocker l'événement
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class GoogleMeetController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // src/Controller/GoogleMeetController.php

    #[Route('/create-meet', name: 'create_meet')]
    public function createMeet(SessionInterface $session, EntityManagerInterface $em): Response
    {
        $googleToken = "ya29.a0AeXRPp5nsOeovPDgUXfNUS31zK93QAHBfm97gs8oxBjxLnCJge1Ohw9QNJeCYLB4nbEZSMfihkaBWyoKuHBl8n3PMWXXHs5dBw0CREYWMIr4e1r9WMxw9kB7af1D_x-65pRmzPr4RrE4jHR4VFsCWlE4oc2jKeVITl3Ty1FOaCgYKATMSARMSFQHGX2MiOUp347JDsnUZLbpo2kYicw0175";

        if (!$googleToken) {
            $this->addFlash('error', 'Vous devez être connecté avec Google pour créer un Meet.');
            return $this->redirectToRoute('connect_google', [
                'action' => 'create_meet',
            ]);
        }


        $meetLink = $this->createGoogleMeet($googleToken);

        if (!$meetLink) {
            $this->addFlash('error', 'Impossible de générer un lien Google Meet.');
            return $this->redirectToRoute('event_create');
        }

        // Sauvegarder le lien dans la base de données
        $event = new Event();
        $event->setTitle("Réunion en ligne");
        $event->setMeetLink($meetLink);
        $event->setDate(new \DateTime());
        $em->persist($event);
        $em->flush();

        $this->addFlash('success', 'Lien Google Meet créé avec succès.');
        return $this->redirectToRoute('event_list');
    }

    /**
     * Fonction pour créer un Google Meet via l'API
     */
    private function createGoogleMeet($googleToken)
    {
        $client = new \Google_Client();
        $client->setAccessToken($googleToken);

        $service = new \Google_Service_Calendar($client);

        $event = new \Google_Service_Calendar_Event([
            'summary' => 'Réunion en ligne',
            'start' => ['dateTime' => (new \DateTime())->format('c')],
            'end' => ['dateTime' => (new \DateTime('+1 hour'))->format('c')],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ]
            ]
        ]);

        $calendarId = 'primary';
        $createdEvent = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

        return $createdEvent->getHangoutLink() ?? null;
    }
}
