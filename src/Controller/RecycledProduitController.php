<?php

namespace App\Controller;
use App\Entity\RecycledProduit;
use App\Form\RecycledProduitType;
use App\Repository\RecycledProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
final class RecycledProduitController extends AbstractController
{  private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $e)
    {
        $this->em = $e;
    }
    #[Route('/recycled/produit', name: 'app_recycled_produit')]
    public function index( ): Response
    {
        $form = $this->createForm(RecycledProduitType::class, new RecycledProduit())->createView();

        return $this->render('recycled_produit/index.html.twig', [
            'form' => $form,
            'controller_name' => 'RecycledProduitController',
        ]);
    }
    #[Route('/recycled/produit/new', name: 'app_recyced_produit_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rproduit = new RecycledProduit();
        $form = $this->createForm(RecycledProduitType::class, $rproduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rproduit);
            $entityManager->flush();

            return $this->redirectToRoute('app_recycled_produit');
        }

        return $this->redirectToRoute('app_recycled_produit');
    }
    
}
