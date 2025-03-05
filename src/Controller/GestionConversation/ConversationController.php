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

final class ConversationController extends AbstractController
{
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
    public function sendMessage(Request $request, EntityManagerInterface $em, HubInterface $hub): Response
    {
        $conversationId = $request->request->get('conversation_id');
        if (!$conversationId) {
            throw new \InvalidArgumentException('Conversation ID is missing');
        }
        $text = $request->request->get('text');

        $conversation = $em->getRepository(Conversation::class)->find($conversationId);

        if (!$conversation) {
            throw $this->createNotFoundException('Conversation introuvable');
        }

        $message = new Messages();
        $message->setText($text);
        $message->setConversation($conversation);
        $message->setDateMessage(new \DateTimeImmutable());
        $message->setSender($this->getUser());

        $em->persist($message);
        $em->flush();

        // Publier le message via Mercure
        $update = new Update(
            'http://localhost:3000/conversations/' . $conversation->getId(),
            json_encode([
                'id' => $message->getId(),
                'text' => $message->getText(),
                'sender' => [
                    'id' => $this->getUser()->getId(),
                    'nom' => $this->getUser()->getNom(),
                    'image' => $this->getUser()->getImage(),
                ],
                'dateMessage' => $message->getDateMessage()->format('H:i A'),
            ])
        );

        $hub->publish($update);

        return $this->json(['status' => 'Message sent!']);
    }
}
