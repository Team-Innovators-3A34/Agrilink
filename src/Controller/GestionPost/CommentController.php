<?php

namespace App\Controller\GestionPost;

use App\Entity\Comment;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\ContentModerationService;
use Psr\Log\LoggerInterface;

#[Route('/comment')]
class CommentController extends AbstractController
{

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/new/{post_id}', name: 'app_comment_new', methods: ['POST'])]
    public function new(Request $request, int $post_id, EntityManagerInterface $entityManager, PostsRepository $postsRepository, ContentModerationService $contentModerationService, LoggerInterface $logger): Response
    {
        $post = $postsRepository->find($post_id);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }


  $commentContent = $request->request->get('content');

        if (!$commentContent) {
            $this->addFlash('error', 'Comment content is required');
            return $this->redirectToRoute('app_post_detail', ['id' => $post->getId()]);
        }

        /// Log incoming comment
    $logger->emergency('COMMENT SUBMISSION', [
        'content' => $commentContent
    ]);

    // Multiple toxicity checks
    $manualToxicityScore = $contentModerationService->manualToxicityScore($commentContent);
    $containsBadWords = $contentModerationService->containsBadWords($commentContent);

    try {
        $apiAnalysis = $contentModerationService->analyzeComment($commentContent);
    } catch (\Exception $e) {
        $apiAnalysis = [
            'toxicity' => 0,
            'profanity' => 0,
            'severe_toxicity' => 0
        ];
    }

        $comment = new Comment();
        $comment->setPost($post);
        $comment->setUserCommented($this->getUser());
        $comment->setContent($request->request->get('content'));
        $comment->setCreatedAt(new \DateTime());

         // Aggressive toxicity filtering
    $toxicityThreshold = 0.5;
    $isToxic = 
        $manualToxicityScore > $toxicityThreshold ||
        $containsBadWords ||
        $apiAnalysis['toxicity'] > $toxicityThreshold ||
        $apiAnalysis['profanity'] > $toxicityThreshold;

    if ($isToxic) {
        $comment->setIsToxic(true);
        $comment->setToxicityScore(max(
            $manualToxicityScore, 
            $apiAnalysis['toxicity'], 
            $apiAnalysis['profanity']
        ));
        
        $this->addFlash('warning', 'Your comment contains potentially inappropriate content.');
        
        // Optional: Prevent posting of toxic comments
        // return $this->redirectToRoute('app_post_detail', ['id' => $post->getId()]);
    }

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_post_detail', ['id' => $comment->getPost()->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/comment/edit/{id}', name: 'app_comment_edit', methods: ['POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): JsonResponse
    {
        // Vérifier si l'utilisateur est propriétaire du commentaire
        if ($this->getUser() !== $comment->getUserCommented()) {
            return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['content']) && !empty($data['content'])) {
            $comment->setContent($data['content']);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'error' => 'Invalid data']);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/deletecomment/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }


        $redirectTo = $request->query->get('redirect_to');

        if ($redirectTo === 'app_postsback') {
            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->redirectToRoute('app_post_detail', ['id' => $comment->getPost()->getId()]);

        /*  return $this->redirectToRoute('app_home'); */
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/backoffice/deletecomment/{id}', name: 'app_comment_delete_back', methods: ['GET', 'POST'])]
    public function deleteback(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();



        return $this->redirectToRoute('app_postsback');

        /*  return $this->redirectToRoute('app_home'); */
    }
}
