<?php

namespace App\Controller\GestionPost;

use App\Entity\Posts;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PostsController extends AbstractController
{
    private $postsRepository;

    public function __construct(PostsRepository $postsRepository)
    {
        $this->postsRepository = $postsRepository;
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/posts', name: 'app_posts')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $posts = $entityManager->getRepository(Posts::class)->findBy(
            [],
            ['created_at' => 'DESC']
        );

        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/posts/new', name: 'app_posts_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post, [
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $imageNames = [];

            foreach ($images as $image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Déplacement du fichier vers le dossier "uploads"
                $image->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $imageNames[] = $newFilename;
            }

            $post->setImages($imageNames);
            $post->setUserId($this->getUser());
            $post->setcreatedAt(new \DateTimeImmutable());

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/post/addPost.html.twig', [
            'form' => $form->createView(),
            'action' => 'add'
        ]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/posts/{id}/edit', name: 'app_posts_edit')]
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PostsType::class, $post, [
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newImages = $form->get('images')->getData();
            $imageDirectory = $this->getParameter('images_directory');

            // Récupérer les anciennes images et les supprimer du serveur
            $existingImages = $post->getImages() ?? [];
            foreach ($existingImages as $oldImage) {
                $oldImagePath = $imageDirectory . '/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Supprime l'ancienne image
                }
            }

            // Nouvelle liste d'images
            $imageNames = [];
            foreach ($newImages as $image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Déplacement du fichier vers le dossier "uploads"
                $image->move($imageDirectory, $newFilename);

                $imageNames[] = $newFilename;
            }

            // Mise à jour de l'entité avec les nouvelles images (remplacement total)
            $post->setImages($imageNames);

            $entityManager->flush();

            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/post/addPost.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'action' => 'edit'
        ]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/posts/{id}/delete', name: 'app_posts_delete')]
    public function delete(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/postDetail/{id}', name: 'app_post_detail')]
    public function postDetail(Posts $post): Response
    {
        return $this->render('frontoffice/post/detailPost.html.twig', [
            'post' => $post
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/backposts/{id}/delete', name: 'app_posts_delete_back')]
    public function delete_back(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_postsback');
    }
}
