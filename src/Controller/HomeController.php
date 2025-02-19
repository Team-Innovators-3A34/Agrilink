<?php

namespace App\Controller;
<<<<<<< HEAD

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('frontoffice/homePage/homePage.html.twig', [
            'controller_name' => 'HomeController',
=======
use App\Entity\Posts;
use App\Form\PostsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostsRepository;
use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController
{
    private $postsRepository;

    public function __construct(PostsRepository $postsRepository)
    {
        $this->postsRepository = $postsRepository;
    }
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Posts::class)->findBy(
            [], // no criteria - get all posts
            ['created_at' => 'DESC'] // order by createdAt descending
        );
        return $this->render('base.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts,
>>>>>>> origin/gestionpost
        ]);
    }
}
