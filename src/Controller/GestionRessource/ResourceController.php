<?php

namespace App\Controller\GestionRessource;

use App\Repository\RessourcesRepository;
use App\Form\ResourceType;
use App\Form\ResourceUpdateType;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ResourceController extends AbstractController
{
    #[Route('/add', name: 'app_add_resource')]
    public function index(): Response
    {
        return $this->render('add.html.twig', [
            'controller_name' => 'ResourceController',
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/settings/ressource', name: 'app_my_resource')]
    public function show_ressource(): Response
    {
        $user = $this->getUser();
        // Récupérer toutes les ressources de la base de données
        $ressources = $user->getRessources();

        return $this->render('frontoffice/settings/ressources/ressources.html.twig', [
            'ressources' => $ressources,
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/show', name: 'app_resourcelist')]
    public function show(RessourcesRepository $ressourceRepository): Response
    {
        // Récupérer toutes les ressources de la base de données
        $ressources = $ressourceRepository->findAll();

        return $this->render('resource/listeResource.html.twig', [
            'ressources' => $ressources,
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route("/settings/ressource/add", name: "app_add_ressource")]
    public function addResource(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $resource = new Ressources();
        $form = $this->createForm(ResourceType::class, $resource, [
            'required' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si nécessaire
                }

                $resource->setImage($newFilename);
            } else {
            }

            $resource->setUserId($this->getUser());
            $entityManager->persist($resource);
            $entityManager->flush();
            // return new Response('ajouté avec succés!');


            // Redirection vers la liste après succès
            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/settings/ressources/addRessource.html.twig', [
            'form' => $form->createView(),
            'action' => 'Ajouter',
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    // Route pour modifier une ressource
    #[Route('/settings/ressource/modifier/{id}', name: 'app_resource_edit')]
    public function updateResource(Request $request, Ressources $resource, EntityManagerInterface $entityManager): Response
    {

        // Créer le formulaire de mise à jour avec les données de la ressource
        $form = $this->createForm(ResourceType::class, $resource);
        $originalImage = $resource->getImage(); // Store original image name before form submission

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Generate a unique file name
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Move the new file to the upload directory
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    // Delete the old image file if it exists
                    if ($originalImage && file_exists($this->getParameter('images_directory') . '/' . $originalImage)) {
                        unlink($this->getParameter('images_directory') . '/' . $originalImage);
                    }

                    // Update the entity with the new file name
                    $resource->setImage($newFilename);
                } catch (FileException $e) {
                }
            } else {
                // If no new file is uploaded, retain the original image
                $resource->setImage($originalImage);
            }

            // Enregistrer les changements
            $entityManager->persist($resource);
            $entityManager->flush();


            // Redirection vers la liste après succès
            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        // Rendu de la vue du formulaire
        return $this->render('frontoffice/settings/ressources/addRessource.html.twig', [
            'form' => $form->createView(),
            'action' => 'Modifier',
            'image' => $resource->getImage(),
        ]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    // Route pour supprimer une ressource
    #[Route('/supprimer/{id}', name: 'app_resource_delete', methods: ['GET', 'POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $ressource = $entityManager->getRepository(Ressources::class)->find($id);

        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        $entityManager->remove($ressource);
        $entityManager->flush();

        return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/ressource/{id}', name: 'app_resource_show')]
    public function showResource(Ressources $ressource): Response
    {
        return $this->render('frontoffice/ressource/detailRessource.html.twig', [
            'ressource' => $ressource,
        ]);
    }
}
