<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Component\HttpFoundation\Request;


final class CategorieController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {   $categorie = new Categorie(); 
        $form = $this->createForm(CategorieType::class, $categorie);
        return $this->render('categorie/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/add_categorie', name: 'add_categorie', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $categorie = new Categorie(); 
        $form = $this->createForm(CategorieType::class, $categorie); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->em->getRepository(Categorie::class)->findOneBy(['nom' => $categorie->getNom()]);

            if ($existing) {
                $this->addFlash("error", "Le nom existe déjà, veuillez en choisir un autre.");
            } else {
                $this->em->persist($categorie);
                $this->em->flush();
                $this->addFlash("message", "Categorie ajoutée avec succès.");
                return $this->redirectToRoute('display_categorie');
            }
        }

        return $this->render('backOffice/Category/addCategory.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/edit_categorie/{id}', name: 'edit_categorie', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id)
    {
    $categorie = $this->em->getRepository(Categorie::class)->find($id); 
    if (!$categorie) {
        throw $this->createNotFoundException('La gategorie n existe pas.');
    }
    $form = $this->createForm(CategorieType::class, $categorie);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $existing = $this->em->getRepository(Categorie::class)->findOneBy(['nom' => $categorie->getNom()]);

        if ($existing && $existing->getId() !== $categorie->getId()) {
            $this->addFlash("error", "Le nom existe déjà, veuillez en choisir un autre.");
        } else {
            $this->em->persist($categorie);
            $this->em->flush();
            return $this->redirectToRoute('display_categorie');
        }
    }
    return $this->render('backOffice/Category/addCategory.html.twig', [
        "form" => $form->createView()
    ]);

    }


    #[Route('/delete_categorie/{id}', name: 'delete_categorie', methods: ['POST', 'GET'])]
    public function delete($id, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->find($id);
        if (!$categorie) {
            $this->addFlash("error", "Catégorie introuvable.");
            return $this->redirectToRoute('display_categorie');
        }

        // Vérifier s'il y a des événements liés avant la suppression
        if (!$categorie->getEvents()->isEmpty()) {
            $this->addFlash("error", "Impossible de supprimer cette catégorie, des événements y sont associés.");
            return $this->redirectToRoute('display_categorie');
        }

        $this->em->remove($categorie);
        $this->em->flush();

        $this->addFlash("message", "Catégorie supprimée avec succès.");
        return $this->redirectToRoute('display_categorie');
    }


    #[Route('/display_categorie', name: 'display_categorie')]
    public function display (ManagerRegistry $managerRegistry): Response
    {
            $em = $managerRegistry->getManager();
            $cat = $em->getRepository(Categorie::class)-> findAll() ;
        return $this->render('backOffice/Category/listCategory.html.twig', [
            'cat' => $cat,
        ]);
    }



}

