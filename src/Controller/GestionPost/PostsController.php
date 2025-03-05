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
use App\Entity\Reaction;
use App\Service\SentimentAnalysisService;
use App\Service\OpenAIService;

final class PostsController extends AbstractController
{
    private $postsRepository;
    private $openAIService;

    
    public function __construct(PostsRepository $postsRepository, OpenAIService $openAIService)
    {
        $this->postsRepository = $postsRepository;
        $this->openAIService = $openAIService;
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
    HuggingFaceImageService $imageService, SentimentAnalysisService $sentimentService, OpenAIService $openAIService): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post, [
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        // Check if we have a tip in session
    $agriculturalTip = $request->getSession()->get('generated_tip');
    if (!$agriculturalTip) {
        // No tip in session, generate one
        $agriculturalTip = $openAIService->generateAgricultureTip();
    }

        // Generate an agricultural tip using OpenAI
    $agriculturalTip = $openAIService->generateAgricultureTip();

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

            $post->setImages($imageNames);
            $post->setUserId($this->getUser());
            $post->setcreatedAt(new \DateTimeImmutable());

             // Set the AI-generated agricultural tip if provided in the form
        if ($request->request->get('ai_tip')) {
            $post->setAiGeneratedTip($request->request->get('ai_tip'));
        }


// Analyse du sentiment du contenu du post
$result = $sentimentService->analyzeFrenchText($post->getDescription());
$post->setSentiment($result['sentiment']);
$post->setSentimentScore($result['score']);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/post/addPost.html.twig', [
            'form' => $form->createView(),
            'action' => 'add',
            'generated_tip' => $agriculturalTip // Pass the tip to your template
        ]);
    }

    #[Route('/generate-tip', name: 'app_generate_tip', methods: ['GET'])]
    public function generateTip(Request $request, OpenAIService $openAIService): Response
    {
        try {
            // Always generate a fresh tip
            $tip = $openAIService->generateAgricultureTip();
            
            // Store the tip in session
            $request->getSession()->set('generated_tip', $tip);
            
            return $this->json(['success' => true, 'tip' => $tip]);
        } catch (\Exception $e) {
            // Log the error and try fallback
            try {
                // If you have a fallback service, use it here
                $fallbackService = $this->get('App\Service\FallbackTipService');
                $tip = $fallbackService->getRandomTip();
                
                $request->getSession()->set('generated_tip', $tip);
                return $this->json(['success' => true, 'tip' => $tip, 'fallback' => true]);
            } catch (\Exception $fallbackError) {
                return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }
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
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager, SluggerInterface $slugger,  SentimentAnalysisService $sentimentService): Response
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

            // Analyse du sentiment du contenu du post mis à jour
        $result = $sentimentService->analyzeFrenchText($post->getDescription());
        $post->setSentiment($result['sentiment']);
        $post->setSentimentScore($result['score']);

            $entityManager->flush();

            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/post/editPost.html.twig', [
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


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/post/{id}/react', name: 'app_post_react', methods: ['POST'])]
public function react(Request $request, Posts $post, EntityManagerInterface $entityManager): JsonResponse {
    try {
        $data = json_decode($request->getContent(), true);
        $reactionType = $data['type'] ?? null;
        
        if (!$reactionType) {
            return new JsonResponse(['success' => false, 'message' => 'Missing reaction type'], 400);
        }
        
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'User not authenticated'], 401);
        }
        
        // Find existing reaction
        $existingReaction = $entityManager->getRepository(Reaction::class)->findOneBy([
            'post' => $post,
            'user' => $user
        ]);
        
        if ($existingReaction) {
            // Update existing reaction
            $existingReaction->setType($reactionType);
        } else {
            // Create new reaction
            $reaction = new Reaction();
            $reaction->setPost($post);
            $reaction->setUser($user);
            $reaction->setType($reactionType);
            $post->addReaction($reaction);
            $entityManager->persist($reaction);
        }
        
        $entityManager->flush();
        
        // Get updated counts using your existing methods
        $counts = [
            'like' => $post->getReactionCountByType('like'),
            'bravo' => $post->getReactionCountByType('bravo'),
            'soutien' => $post->getReactionCountByType('soutien'),
            'instructif' => $post->getReactionCountByType('instructif'),
            'drole' => $post->getReactionCountByType('drole')
        ];
        
        return new JsonResponse([
            'success' => true,
            'counts' => $counts
        ]);
    } catch (\Exception $e) {
        return new JsonResponse([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

    /* #[Route('/post/{id}/react', name: 'app_post_react', methods: ['POST'])]
    public function react(Request $request, Posts $post, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $reactionType = $data['type'] ?? null;
            
            if (!$reactionType) {
                return $this->json(['success' => false, 'message' => 'Missing reaction type'], 400);
            }
            
            $user = $this->getUser();
            if (!$user) {
                return $this->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }
            
            // Add or update the reaction
            $this->postService->addReaction($post, $user, $reactionType);
            
            // Get updated counts
            $counts = [
                'like' => $post->getReactionCountByType('like'),
                'bravo' => $post->getReactionCountByType('bravo'),
                'soutien' => $post->getReactionCountByType('soutien'),
                'instructif' => $post->getReactionCountByType('instructif'),
                'drole' => $post->getReactionCountByType('drole')
            ];
            
            return $this->json([
                'success' => true,
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Reaction error: ' . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Only include this temporarily for debugging
                'file' => $e->getFile(),           // Only include this temporarily for debugging
                'line' => $e->getLine()            // Only include this temporarily for debugging
            ], 500);
        }
    } */
}