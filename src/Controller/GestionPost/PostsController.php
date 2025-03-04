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
use App\Service\HuggingFaceImageService;


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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger,
    HuggingFaceImageService $imageService): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post, [
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $imageNames = [];

            
             // Vérifier si une image IA a été générée
        $generatedImagePath = $request->request->get('generated_image_path');
        if ($generatedImagePath) {
            // Si l'image est dans un dossier temporaire, la déplacer
            $originalFilename = basename($generatedImagePath);
            $newFilename = 'ai-generated-' . uniqid() . '.jpg';
            
            // Copier l'image générée vers le dossier images_directory
            copy(
                'public/' . $generatedImagePath,
                $this->getParameter('images_directory') . '/' . $newFilename
            );
            
            $imageNames[] = $newFilename;
        }

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
            $this->getUser()->setScore($this->getUser()->getScore() + 10);

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

    #[Route('/generate-image', name: 'app_generate_image')]
public function generateImage(Request $request, HuggingFaceImageService $imageService): JsonResponse
{
    $description = $request->request->get('description');
    
    if (!$description) {
        return new JsonResponse(['success' => false, 'error' => 'Description manquante']);
    }
    
    try {
        $imagePath = $imageService->generateImageFromDescription($description);
        return new JsonResponse(['success' => true, 'imagePath' => $imagePath]);
    } catch (\Exception $e) {
        return new JsonResponse(['success' => false, 'error' => $e->getMessage()]);
    }
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
        $this->getUser()->setScore($this->getUser()->getScore() - 10);

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

    
    #[Route('/posts/{id}/react', name: 'app_post_reaction', methods: ['POST'])]
    #[IsGranted('ROLE_USER')] // This allows any logged-in user to like/dislike
    public function react(Request $request, Posts $post, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $type = $request->request->get('type');
        
        if (!in_array($type, ['like', 'dislike'])) {
            return new JsonResponse(['error' => 'Invalid reaction type'], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $currentReaction = $post->getUserReaction($user);
            
            if ($currentReaction === $type) {
                // User clicked same reaction, remove it
                if ($type === 'like') {
                    $post->removeLike($userId);
                } else {
                    $post->removeDislike($userId);
                }
                $newReaction = null;
            } else {
                // Remove any existing opposite reaction
                if ($currentReaction === 'like' && $type === 'dislike') {
                    $post->removeLike($userId);
                } elseif ($currentReaction === 'dislike' && $type === 'like') {
                    $post->removeDislike($userId);
                }
                
                // Add new reaction
                if ($type === 'like') {
                    $post->addLike($userId);
                } else {
                    $post->addDislike($userId);
                }
                $newReaction = $type;
            }
            
            $entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Reaction updated',
                'likes' => $post->getLikesCount(),
                'dislikes' => $post->getDislikesCount(),
                'userReaction' => $newReaction
            ]);
        } catch (\Exception $e) {
            // Log the error
            return new JsonResponse([
                'error' => 'An error occurred while processing your reaction',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
