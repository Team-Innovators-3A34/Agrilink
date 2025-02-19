<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/new/{post_id}', name: 'app_comment_new', methods: ['POST'])]
    public function new(Request $request, int $post_id, EntityManagerInterface $entityManager, PostsRepository $postsRepository): Response
    {
        $post = $postsRepository->find($post_id);
        
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $comment = new Comment();
     /*    $comment->setPostIdId($post); */
     /*   $comment->setPostIdId($post->getId());  */
       $comment->setPost($post); 
       $comment->setContent($request->request->get('content'));
        $comment->setCreatedAt(new \DateTime());

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            
            if ($content) {
                $comment->setContent($content);
                $entityManager->flush();
                
                return $this->redirectToRoute('app_home');
            }
        }
        
        return $this->render('comment/editcomm.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Comment deleted successfully!');

        $redirectTo = $request->query->get('redirect_to');
        
        if ($redirectTo === 'app_postsback') {
            return $this->redirectToRoute('app_postsback');
        }
        
        return $this->redirectToRoute('app_home');

       /*  return $this->redirectToRoute('app_home'); */
    }
}