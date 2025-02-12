<?php

namespace App\Controller;
use App\Repository\RessourcesRepository;
use App\Form\ResourceType ;
use App\Form\ResourceUpdateType ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ressources;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Extension\Core\DataTransformer\CallbackTransformer;

final class ResourceController extends AbstractController
{
    #[Route('/add', name: 'app_add_resource')]
    public function index(): Response
    {
        return $this->render('add.html.twig', [
            'controller_name' => 'ResourceController',
        ]);
    }
    #[Route('/resourceshow', name: 'app_resource')]
    public function index1(RessourcesRepository $ressourceRepository): Response
    {
        // Récupérer toutes les ressources de la base de données
        $ressources = $ressourceRepository->findAll();

        return $this->render('resourceshow.html.twig', [
            'ressources' => $ressources,
        ]);
        
    }
    #[Route('/show', name: 'app_resourcelist')]
    public function show(RessourcesRepository $ressourceRepository): Response
    {
        // Récupérer toutes les ressources de la base de données
        $ressources = $ressourceRepository->findAll();

        return $this->render('resource/listeResource.html.twig', [
            'ressources' => $ressources,
        ]);
        
    }

    #[Route("/ajouter", name: "ajouter")]
    public function addResource(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $resource = new Ressources();
    $form = $this->createForm(ResourceType::class, $resource);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);  // Utilisation du slugger ici
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'), // Configuré dans services.yaml
                    $newFilename
                );
            } catch (FileException $e) {
               
            }

            $resource->setImage($newFilename);
        }

        $entityManager->persist($resource);
        $entityManager->flush();
        return new Response('ajouté avec succés!');
        $this->addFlash('success', 'Ressource ajoutée avec succès !');
       
    }

    return $this->render('resource/index.html.twig', [
        'form' => $form->createView(),
    ]);
}
// Route pour modifier une ressource
#[Route('/modifier/{id}', name: 'app_resource_edit')]
public function updateResource(Request $request, Ressources $resource ,EntityManagerInterface $entityManager): Response
{

   // Créer le formulaire de mise à jour avec les données de la ressource
   $form = $this->createForm(ResourceUpdateType::class, $resource);

   // Gérer la soumission du formulaire
   $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
       // Enregistrer les changements
       $entityManager->persist($resource);
       $entityManager->flush();

      
   }

   // Rendu de la vue du formulaire
   return $this->render('resource/edit.html.twig', [
       'form' => $form->createView(),
       'resource' => $resource,
   ]);
   
}


// Route pour supprimer une ressource
#[Route('/supprimer/{id}', name: 'app_resource_delete', methods: ['POST'])]
public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $ressource = $entityManager->getRepository(Ressources::class)->find($id);

        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        $entityManager->remove($ressource);
        $entityManager->flush();

        $this->addFlash('success', 'Ressource supprimée avec succès.');

        return $this->redirectToRoute('app_resourcelist'); // Change 'resource_list' avec la route correcte
    }
}