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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $post = new Posts();
    $form = $this->createForm(PostsType::class, $post);
    $form->handleRequest($request);
    
     
    if ($request->server->get('CONTENT_LENGTH') > (int) ini_get('post_max_size') * 1024 * 1024) {
        $this->addFlash('danger', 'The uploaded file is too large. Maximum size: 10MB');
        return $this->redirectToRoute('app_posts_new');
    }

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $fileUpload = $form->get('fileUpload')->getData();

            if ($fileUpload) {
                $originalFilename = pathinfo($fileUpload->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fileUpload->guessExtension();

                try {
                    $fileUpload->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                    
                    $post->setFile($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading your file');
                    return $this->render('posts/new.html.twig', [
                        'form' => $form->createView()
                    ]);
                }
            }

            $post->setUserIdId(1);
            $post->setcreatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Post created successfully!');
            return $this->redirectToRoute('app_home');
        }
    }

    return $this->render('posts/new.html.twig', [
        'form' => $form->createView()
    ]);
}

#[Route('/posts/{id}/edit', name: 'app_posts_edit')]
public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $oldFile = $post->getFile();
    $form = $this->createForm(PostsType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        // Add explicit validation check
        if (empty($form->get('description')->getData())) {
            $form->get('description')->addError(new FormError('Description cannot be empty'));
        }

        if ($form->isValid()) {
            $fileUpload = $form->get('fileUpload')->getData();
            
            if ($fileUpload) {
                $originalFilename = pathinfo($fileUpload->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fileUpload->guessExtension();
                
                try {
                    $fileUpload->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                    
                    // Delete old file if it exists
                    if ($oldFile) {
                        $oldFilePath = $this->getParameter('files_directory').'/'.$oldFile;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    
                    $post->setFile($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading your file');
                    return $this->render('posts/edit.html.twig', [
                        'form' => $form->createView(),
                        'post' => $post
                    ]);
                }
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Post updated successfully!');
            return $this->redirectToRoute('app_home');
        }
    }

    return $this->render('posts/edit.html.twig', [
        'form' => $form->createView(),
        'post' => $post
    ]);
}
  

    #[Route('/posts/{id}/delete', name: 'app_posts_delete')]
    public function delete(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        // For testing purposes, we're allowing deletion for all posts
        $entityManager->remove($post);
        $entityManager->flush();
        
        $this->addFlash('success', 'Post deleted successfully!');


    $redirectTo = $request->query->get('redirect_to');
    
    if ($redirectTo === 'app_postsback') {
        return $this->redirectToRoute('app_postsback');
    }
    
    return $this->redirectToRoute('app_home');
       /*  return $this->redirectToRoute('app_home'); */
    }


}
