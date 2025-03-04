<?php

namespace App\Controller\GestionConversation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Messages;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ConversationController extends AbstractController
{
    private $em;
    private HttpClientInterface $httpClient;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $httpClient)
    {
        $this->em = $em;
        $this->httpClient = $httpClient;
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/chat', name: 'app_chat')]
    public function test(): Response
    {
        return $this->render('frontOffice/chat/chat.html.twig');
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/conversation/start/{userId}', name: 'start_conversation')]
    public function startConversation(int $userId, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();
        $otherUser = $em->getRepository(User::class)->find($userId);

        $conversation = $em->getRepository(Conversation::class)->findExistingConversation($currentUser, $otherUser);

        if (!$conversation) {
            // Créer une nouvelle conversation
            $conversation = new Conversation();
            $conversation->addPersonne($currentUser);
            $conversation->addPersonne($otherUser);

            $em->persist($conversation);
            $em->flush();
        }

        return $this->redirectToRoute('view_conversation', ['conversationId' => $conversation->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/conversation/{conversationId}', name: 'view_conversation')]
    public function viewConversation(int $conversationId, EntityManagerInterface $em): Response
    {
        $conversation = $em->getRepository(Conversation::class)->find($conversationId);

        if (!$conversation) {
            throw $this->createNotFoundException('Conversation introuvable');
        }

        return $this->render('frontOffice/chat/chat.html.twig', [
            'conversation' => $conversation,
            'messages' => $conversation->getMessages(),
            'mercurePublicUrl' => $_ENV['MERCURE_PUBLIC_URL'], // Passer l'URL Mercure au template
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/message/send', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request): Response
    {
        $conversationid = $request->request->get('conversation_id');
        $text = $request->request->get('text');

        $conversation = $this->em->getRepository(Conversation::class)->find($conversationid);
        $message = new Messages();
        $message->setConversation($conversation);
        $message->setSender($this->getUser());
        $message->setText($text);
        $message->setDateMessage(new \DateTime());

        $this->em->persist($message);
        $this->em->flush();

        $topic = 'http://localhost:3000/conversations/' . $conversation->getId();
        $data = json_encode([
            'id' => $message->getId(),
            'text' => $message->getText(),
            'sender' => [
                'id' => $this->getUser()->getId(),
                'nom' => $this->getUser()->getNom(),
                'image' => $this->getUser()->getImage(),
            ],
            'dateMessage' => $message->getDateMessage()->format('H:i A'),
        ]);

        $response = $this->httpClient->request('POST', "http://localhost:3000/.well-known/mercure", [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.KKPIikwUzRuB3DTpVw6ajzwSChwFw5omBMmMcWKiDcM',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query([
                'topic' => $topic,
                'data' => $data
            ])
        ]);

        return new Response('', $response->getStatusCode());
    }
}
