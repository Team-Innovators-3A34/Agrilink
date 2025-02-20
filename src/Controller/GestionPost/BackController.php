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

    #[IsGranted("ROLE_ADMIN")]
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
    }
}
