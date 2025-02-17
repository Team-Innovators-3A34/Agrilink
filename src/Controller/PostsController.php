<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormInterface;

final class PostsController extends AbstractController 
{
    private $postsRepository;

    public function __construct(PostsRepository $postsRepository)
    {
        $this->postsRepository = $postsRepository;
    }
    
    #[Route('/posts', name: 'app_posts')]
    public function index(EntityManagerInterface $entityManager): Response
    {
       
        $posts = $entityManager->getRepository(Posts::class)->findBy(
            [], // no criteria - get all posts
            ['created_at' => 'DESC'] // order by createdAt descending
        );
        

        // Render the template with the posts
        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/posts/new', name: 'app_posts_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* $post->setUserIdId($this->getUser()); */
            $post->setUserIdId(1); 
            $post->setcreatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($post);
            $entityManager->flush();

        // Add a flash message
            $this->addFlash('success', 'Post created successfully!');
            
            // Redirect to the posts listing page
            return $this->redirectToRoute('app_home');
        }

        return $this->render('posts/new.html.twig', [
            'form' => $form->createView(),
        ]);

          
}

    #[Route('/posts/{id}/edit', name: 'app_posts_edit')]
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        // For testing purposes, we're allowing edit for all posts
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Post updated successfully!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('posts/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    #[Route('/posts/{id}/delete', name: 'app_posts_delete')]
    public function delete(Posts $post, EntityManagerInterface $entityManager): Response
    {
        // For testing purposes, we're allowing deletion for all posts
        $entityManager->remove($post);
        $entityManager->flush();
        
        $this->addFlash('success', 'Post deleted successfully!');
        return $this->redirectToRoute('app_home');
    }


}
