<?php

namespace App\Controller\GestionEvenement;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EventRepository;
use App\Entity\Event;
use App\Form\EventType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route as x;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EventController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        $form = $this->createForm(EventType::class);

        return $this->render('frontOffice/Event/listEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/addEvent', name: 'add_event', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the event name already exists
            $existingEvent = $this->em->getRepository(Event::class)->findOneBy(['nom' => $event->getNom()]);

            if ($existingEvent) {
                $this->addFlash("error", "Le nom existe déjà, veuillez en choisir un autre.");
            } else {
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('image')->getData();

                if ($imageFile) {
                    // Generate a unique name for the file
                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        // Move the file to the configured directory (ensure the directory exists)
                        $imageFile->move(
                            $this->getParameter('images_directory'),  // Defined in services.yaml
                            $newFilename
                        );

                        // Store only the filename in the database
                        $event->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                }

                $this->em->persist($event);
                $this->em->flush();
                $this->addFlash("message", "Événement ajouté avec succès.");

                return $this->redirectToRoute('display_event');
            }
        }

        return $this->render('backOffice/evenement/addEvenement.html.twig', [
            "form" => $form->createView(),
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/edit_event/{id}', name: 'edit_event', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $event = $this->em->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement n\'existe pas.');
        }

        $originalImage = $event->getImage(); // Store original image name before form submission

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingEvent = $this->em->getRepository(Event::class)->findOneBy(['nom' => $event->getNom()]);

            if ($existingEvent && $existingEvent->getId() !== $event->getId()) {
                $this->addFlash("error", "Le nom existe déjà, veuillez en choisir un autre.");
            } else {
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
                        $event->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                } else {
                    // If no new file is uploaded, retain the original image
                    $event->setImage($originalImage);
                }

                $this->em->flush();
                $this->addFlash("message", "Événement mis à jour avec succès.");

                return $this->redirectToRoute('display_event');
            }
        }

        return $this->render('backOffice/evenement/addEvenement.html.twig', [
            "form" => $form->createView(),
            "image" => $event->getImage(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/Delete/{id}', name: 'Delete')]
    public function delete($id, ManagerRegistry $managerRegistry, EventRepository $EventRepository): Response
    {
        $em = $managerRegistry->getManager();
        $play = $EventRepository->find($id);
        $this->em->remove($play);
        $this->em->flush();
        return $this->redirectToRoute('display_event');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/event', name: 'display_event', methods: ['GET', 'POST'])]
    public function displayEventBackoffice(Request $request, EventRepository $eventRepository): Response
    {
        $searchNom = $request->query->get('nom');
        $searchDate = $request->query->get('date');
        $searchAdresse = $request->query->get('adresse');

        $sortField = $request->query->get('sort', 'nom'); // Default sort by 'nom'
        $sortDirection = $request->query->get('direction', 'asc'); // Default order 'asc'

        $date = $searchDate ? \DateTime::createFromFormat('Y-m-d', $searchDate) : null;

        $events = $eventRepository->findByFilters($searchNom, $date, $searchAdresse, $sortField, $sortDirection);

        return $this->render('backOffice/evenement/listEvenement.html.twig', [
            'events' => $events,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }




    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/Event', name: 'display_event_front', methods: ['GET', 'POST'])]
    public function display_front(Request $request, EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('frontOffice/evenement/listEvenement.html.twig', [
            'events' => $events,
        ]);
    }



    #[Route('/update-event-places/{id}', name: 'update_event_places', methods: ['GET', 'POST'])]
    public function updatePlaces(Request $request, int $id, EventRepository $eventRepository, EntityManagerInterface $em): JsonResponse
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], 404);
        }

        $event->addParticipant($this->getUser());
        
        $data = json_decode($request->getContent(), true);
        $nbrPlaces = $data['nbr_places'] ?? null;

        if ($nbrPlaces !== null && $nbrPlaces >= 0) {
            $event->setNbrPlaces($nbrPlaces); // Assuming the Event entity has a setNbrPlaces method
            $em->persist($event);
            $em->flush();

            return new JsonResponse(['message' => 'Number of places updated successfully']);
        }

        return new JsonResponse(['error' => 'Invalid data'], 400);
    }
}
