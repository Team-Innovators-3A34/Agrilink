<?php

namespace App\Controller\GestionPointRecylcage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PointrecyclageRepository;
use App\Entity\Pointrecyclage;
use App\Form\PointrecyclageType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;


final class PointrecyclageController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[Route('/formPointfront', name: 'addpoint_front', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $form = $this->createForm(PointrecyclageType::class);

        return $this->render('frontOffice/pointrecyclage/addpoint.html.twig', [
            'form' => $form->createView(),
            "action" => "add",

        ]);
    }
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[Route('/frontoffice/addPoint', name: 'add_point_front', methods: ['GET', 'POST'])]
    public function create_front(Request $request): Response
    {
        $Pointrecyclage = new Pointrecyclage();
        $form = $this->createForm(PointrecyclageType::class, $Pointrecyclage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the event name already exists
            $existingPoint = $this->em->getRepository(Pointrecyclage::class)->findOneBy(['name' => $Pointrecyclage->getName()]);

            if ($existingPoint) {
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
                        $Pointrecyclage->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                }
                $this->getUser()->setScore($this->getUser()->getScore() + 10);

                // Set the createdAt date to the current system date
                $Pointrecyclage->setCreatedAt(new \DateTimeImmutable());
                $Pointrecyclage->setOwner($this->getUser());

                $this->em->persist($Pointrecyclage);
                $this->em->flush();

                return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
            }
        }

        return $this->render('frontoffice/pointrecyclage/addPoint.html.twig', [
            "form" => $form->createView(),
            "action" => "add",
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]

    #[Route('/dashboard/addPoint', name: 'add_point', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $Pointrecyclage = new Pointrecyclage();
        $form = $this->createForm(PointrecyclageType::class, $Pointrecyclage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the event name already exists
            $existingPoint = $this->em->getRepository(Pointrecyclage::class)->findOneBy(['name' => $Pointrecyclage->getName()]);

            if ($existingPoint) {
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
                        $Pointrecyclage->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                }

                
                // Set the createdAt date to the current system date
                $Pointrecyclage->setCreatedAt(new \DateTimeImmutable());
                $Pointrecyclage->setOwner($this->getUser());

                $this->em->persist($Pointrecyclage);
                $this->em->flush();
                $this->addFlash("message", "Point ajouté avec succès.");

                return $this->redirectToRoute('display_point');
            }
        }

        return $this->render('backOffice/pointrecyclage/addPoint.html.twig', [
            "form" => $form->createView(),
            "action" => "add",
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/point', name: 'display_point', methods: ['GET', 'POST'])]
    public function displayPointBackoffice(Request $request, PointrecyclageRepository $PointrecyclageRepository): Response
    {
        $searchNom = $request->query->get('name');
        $searchDate = $request->query->get('createdAt');
        $searchAdresse = $request->query->get('adresse');

        $sortField = $request->query->get('sort', 'name'); // Default sort by 'nom'
        $sortDirection = $request->query->get('direction', 'asc'); // Default order 'asc'

        $date = $searchDate ? \DateTime::createFromFormat('Y-m-d', $searchDate) : null;

        $points = $PointrecyclageRepository->findAll();

        return $this->render('backOffice/pointrecyclage/listPoint.html.twig', [
            'points' => $points,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete_point/{id}', name: 'delete_point')]
    public function delete($id, ManagerRegistry $managerRegistry, PointrecyclageRepository $PointrecyclageRepository): Response
    {
        $em = $managerRegistry->getManager();
        $play = $PointrecyclageRepository->find($id);
        $this->em->remove($play);
        $this->em->flush();
        return $this->redirectToRoute('display_point');
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/edit_point/{id}', name: 'edit_point', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $Pointrecyclage = $this->em->getRepository(Pointrecyclage::class)->find($id);

        if (!$Pointrecyclage) {
            throw $this->createNotFoundException('Le point de recyclage n\'existe pas.');
        }

        $originalImage = $Pointrecyclage->getImage(); // Store original image name before form submission

        $form = $this->createForm(PointrecyclageType::class, $Pointrecyclage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingPoint = $this->em->getRepository(Pointrecyclage::class)->findOneBy(['name' => $Pointrecyclage->getName()]);

            if ($existingPoint && $existingPoint->getId() !== $Pointrecyclage->getId()) {
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
                        $Pointrecyclage->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                } else {
                    // If no new file is uploaded, retain the original image
                    $Pointrecyclage->setImage($originalImage);
                }

                $this->em->flush();
                $this->addFlash("message", "Point de recyclage mis à jour avec succès.");

                return $this->redirectToRoute('display_point');
            }
        }

        return $this->render('backOffice/pointrecyclage/addPoint.html.twig', [
            "form" => $form->createView(),
            "image" => $Pointrecyclage->getImage(),
            "action" => "edit",
        ]);
    }
    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/Point', name: 'display_point_front', methods: ['GET', 'POST'])]
    public function display_front(Request $request, PointrecyclageRepository $pointrecyclageRepository): Response
    {
        $points = $pointrecyclageRepository->findAll();

        return $this->render('frontOffice/pointrecyclage/index.html.twig', [
            'points' => $points,
        ]);
    }
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[Route('/delete_point_front/{id}', name: 'delete_point_front')]
    public function delete_front($id, ManagerRegistry $managerRegistry, PointrecyclageRepository $PointrecyclageRepository): Response
    {
        $em = $managerRegistry->getManager();
        $play = $PointrecyclageRepository->find($id);
        $this->getUser()->setScore($this->getUser()->getScore() - 10);

        $this->em->remove($play);
        $this->em->flush();
        return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
    }
    #[Route('/frontoffice/edit_point/{id}', name: 'edit_point_front', methods: ['GET', 'POST'])]
    public function edit_front(Request $request, int $id): Response
    {
        $Pointrecyclage = $this->em->getRepository(Pointrecyclage::class)->find($id);

        if (!$Pointrecyclage) {
            throw $this->createNotFoundException('Le point de recyclage n\'existe pas.');
        }

        $originalImage = $Pointrecyclage->getImage(); // Store original image name before form submission

        $form = $this->createForm(PointrecyclageType::class, $Pointrecyclage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingPoint = $this->em->getRepository(Pointrecyclage::class)->findOneBy(['name' => $Pointrecyclage->getName()]);

            if ($existingPoint && $existingPoint->getId() !== $Pointrecyclage->getId()) {
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
                        $Pointrecyclage->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                } else {
                    // If no new file is uploaded, retain the original image
                    $Pointrecyclage->setImage($originalImage);
                }

                $this->em->flush();
                $this->addFlash("message", "Point de recyclage mis à jour avec succès.");

                return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
            }
        }

        return $this->render('frontoffice/pointrecyclage/addPoint.html.twig', [
            "form" => $form->createView(),
            "image" => $Pointrecyclage->getImage(),
            "action" => "edit",
        ]);
    }
    #[Route('/frontoffice/affichageproduit', name: 'app_display_prod')]
    #[IsGranted('ROLE_AGRICULTURE')]

    public function display_prod(): Response
    {
        $user = $this->getUser();

        return $this->render('frontoffice/pointrecyclage/affichageproduit.html.twig', [
        'user' => $user,

        ]);
    }
}
