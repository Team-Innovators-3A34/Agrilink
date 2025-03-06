<?php

namespace App\Controller\GestionPost;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Posts;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BackController extends AbstractController
{
    #[Route('/back', name: 'app_back')]
    public function index(): Response
    {
        return $this->render('backoffice/base.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }

   /*  #[IsGranted("ROLE_ADMIN")]
    #[Route('/dashboard/post', name: 'app_postsback')]
    public function index1(EntityManagerInterface $entityManager): Response
    {

        $posts = $entityManager->getRepository(Posts::class)->findBy(
            [], // no criteria - get all posts
            ['created_at' => 'DESC'] // order by createdAt descending
        );

        // Render the template with the posts
        return $this->render('backoffice/post/posts.html.twig', [
            'posts' => $posts,
        ]);
    } */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/dashboard/post', name: 'app_postsback')]
    public function index1(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Define the emotions array - make sure these match exactly what's in your database
        $emotions = ['positive', 'negative', 'neutral'];
        
        // Define the post types
        $postTypes = ['article', 'question', 'discussion'];
        
        // Get all users who have posted
        $userRepository = $entityManager->getRepository('App\Entity\User');
        $users = $userRepository->createQueryBuilder('u')
            ->select('u')
            ->join('App\Entity\Posts', 'p', 'WITH', 'p.user_id = u.id')
            ->distinct()
            ->getQuery()
            ->getResult();
        
        // Create query builder for filtering
        $repository = $entityManager->getRepository(Posts::class);
        $queryBuilder = $repository->createQueryBuilder('p')
                                  ->orderBy('p.created_at', 'DESC');
        
        // Apply filters if they exist
        $dateFrom = $request->query->get('dateFrom');
        $dateTo = $request->query->get('dateTo');
        $selectedEmotions = $request->query->all('emotions');
        $title = $request->query->get('title');
        $types = $request->query->all('types');
        $userIds = $request->query->all('users');
        
        if ($dateFrom) {
            $dateFrom = new \DateTime($dateFrom);
            $queryBuilder->andWhere('p.created_at >= :dateFrom')
                        ->setParameter('dateFrom', $dateFrom->format('Y-m-d 00:00:00'));
        }
        
        if ($dateTo) {
            $dateTo = new \DateTime($dateTo);
            $queryBuilder->andWhere('p.created_at <= :dateTo')
                        ->setParameter('dateTo', $dateTo->format('Y-m-d 23:59:59'));
        }
        
        if (!empty($selectedEmotions)) {
            $queryBuilder->andWhere('p.sentiment IN (:emotions)')
                        ->setParameter('emotions', $selectedEmotions);
        }
        
        if ($title) {
            $queryBuilder->andWhere('p.title LIKE :title')
                        ->setParameter('title', '%' . $title . '%');
        }
        
        if (!empty($types)) {
            $queryBuilder->andWhere('p.type IN (:types)')
                        ->setParameter('types', $types);
        }
        
        if (!empty($userIds)) {
            $queryBuilder->andWhere('p.user_id IN (:userIds)')
                        ->setParameter('userIds', $userIds);
        }
        
        $posts = $queryBuilder->getQuery()->getResult();
    
        // For AJAX requests, return JSON
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('backoffice/post/_posts_table.html.twig', [
                    'posts' => $posts
                ])
            ]);
        }
    
        // For regular requests, render the full page
        return $this->render('backoffice/post/posts.html.twig', [
            'posts' => $posts,
            'emotions' => $emotions,
            'postTypes' => $postTypes,
            'users' => $users,
            'selectedEmotions' => $selectedEmotions
        ]);
    }
}
