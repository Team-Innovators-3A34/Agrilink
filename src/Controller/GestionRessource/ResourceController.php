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
use App\Entity\RatingRessource;
use Symfony\Component\Form\Extension\Core\DataTransformer\CallbackTransformer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\WeatherService;

final class ResourceController extends AbstractController
{
    #[Route('/add', name: 'app_add_resource')]
    public function index(): Response
    {
        return $this->render('add.html.twig', [
            'controller_name' => 'ResourceController',
        ]);
    }
    #[Route('/weather', name: 'weather')]
    public function index44(Request $request, WeatherService $weatherService)
    {
        $city = $request->query->get('city', 'Tunis'); // Ville par défaut
        $weatherData = $weatherService->getWeather($city);

        return $this->render('frontoffice/homepage/index.html.twig', [
            'weather' => $weatherData,
            'city' => $city
        ]);
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/resource/{id}/rate', name: 'resource_rate', methods: ['POST'])]
    // Dans le contrôleur ou la méthode de traitement du formulaire
    public function rateResource(Request $request, Ressources $resource, EntityManagerInterface $entityManager)
    {
        // Récupérer la note soumise depuis le formulaire
        $newRating = (int) $request->get('rating');

        // Calculer le nouveau rating en prenant en compte le rating actuel et le ratingCount
        $currentRating = $resource->getRating(); // Récupérer le rating actuel
        $currentRatingCount = $resource->getRatingCount(); // Récupérer le ratingCount actuel

        // Si il y a déjà des notes
        if ($currentRatingCount > 0) {
            // Calculer la moyenne pondérée (ou une autre formule selon ton besoin)
            $newRatingValue = (($currentRating * $currentRatingCount) + $newRating) / ($currentRatingCount + 1);
        } else {
            // Si aucune note, on met directement la nouvelle note
            $newRatingValue = $newRating;
        }

        // Mettre à jour le ratingCount (incrémenter de 1)
        $resource->setRatingCount($currentRatingCount + 1);

        // Mettre à jour le rating avec la nouvelle valeur calculée
        $resource->setRating($newRatingValue);


        $entityManager->persist($resource);
        $entityManager->flush();

        // Rediriger ou afficher un message
        return $this->redirectToRoute('app_profilee', ['id' => $resource->getId()]);
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
            $images = $form->get('images')->getData();
            $imageNames = [];

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

            $resource->setImages($imageNames); // Stocker les noms des fichiers sous forme de JSON
            $resource->setUserId($this->getUser());
            $this->getUser()->setScore($this->getUser()->getScore() + 10);
            $entityManager->persist($resource);
            $entityManager->flush();

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
    #[Route('/settings/ressource/modifier/{id}', name: 'app_resource_edit')]
    public function updateResource(Request $request, Ressources $resource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResourceType::class, $resource);
        $originalImages = $resource->getImages(); // Récupérer les anciennes images

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $imageFiles */
            $imageFiles = $form->get('images')->getData(); // Récupérer les nouvelles images

            // Supprimer toutes les anciennes images
            if (!empty($originalImages)) {
                foreach ($originalImages as $oldImage) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Supprimer le fichier
                    }
                }
            }

            $newImages = []; // Liste des nouvelles images

            if ($imageFiles) {
                foreach ($imageFiles as $imageFile) {
                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                        $newImages[] = $newFilename; // Ajouter au tableau des nouvelles images
                    } catch (FileException $e) {
                        // Gérer l'erreur
                    }
                }
            }

            $resource->setImages($newImages); // Mettre à jour la base de données avec les nouvelles images
            $entityManager->persist($resource);
            $entityManager->flush();

            return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontoffice/settings/ressources/addRessource.html.twig', [
            'form' => $form->createView(),
            'resource' => $resource,
            'action' => 'Modifier',
            'image' => "",
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
        $this->getUser()->setScore($this->getUser()->getScore() - 10);


        $entityManager->remove($ressource);
        $entityManager->flush();

        return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/ressource/{id}', name: 'app_resource_show')]
    public function showResource(Ressources $ressource, EntityManagerInterface $em): Response
    {
        $MyRate = $em->getRepository(RatingRessource::class)->findOneBy([
            'ressource' => $ressource,
            'user' => $this->getUser()
        ]);
        return $this->render('frontoffice/ressource/test.html.twig', [
            'ressource' => $ressource,
            'MyRate' => $MyRate,
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/ressource/add/rate/{id}', name: 'app_add_rate')]
    public function addRate(Request $request, Ressources $ressource, EntityManagerInterface $entityManager): Response
    {
        $rate = $request->get('rate');
        $ratingRessource = new RatingRessource();
        $ratingRessource->setRate($rate);
        $ratingRessource->setRatedAt(new \DateTimeImmutable());
        $ratingRessource->setUser($this->getUser());
        $ratingRessource->setRessource($ressource);

        $entityManager->persist($ratingRessource);
        $entityManager->flush();

        return $this->redirectToRoute('app_resource_show', ['id' => $ressource->getId()]);
    }


    #[Route('/testtest', name: 'test')]
    public function test(): Response
    {

        return $this->render('test.html.twig');
    }
}
