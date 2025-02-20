<?php

namespace App\Controller\GestionUser;

use App\Entity\Event;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Posts;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {

        $posts = $this->em->getRepository(Posts::class)->findAll();
        shuffle($posts);

        $events = $this->em->getRepository(Event::class)->findAll();

        return $this->render('frontoffice/homePage/homePage.html.twig', [
            'posts' => $posts,
            'events' => $events
        ]);
    }
}
