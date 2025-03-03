<?php

namespace App\Controller\GestionPointRecylcage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProduitrecyclageRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produitrecyclage;
use App\Form\ProduitrecyclageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\PointrecyclageRepository;
use App\Entity\Pointrecyclage;
use App\Form\PointrecyclageType;


final class ProduitrecyclageController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/produitrecyclage', name: 'app_produitrecyclage')]
    public function index(): Response
    {
        return $this->render('produitrecyclage/index.html.twig', [
            'controller_name' => 'ProduitrecyclageController',
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/frontoffice/addProduitfront/{id}', name: 'front_add_produit', methods: ['GET', 'POST'])]
    public function create_front(Request $request, $id, PointrecyclageRepository $pointrecyclageRepository)
    {
        $prod = new Produitrecyclage();
        $form = $this->createForm(ProduitrecyclageType::class, $prod);
        $form->handleRequest($request);
        $pointRecyclage = $pointrecyclageRepository->find($id);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->em->getRepository(Produitrecyclage::class)->findOneBy(['name' => $prod->getName()]);

            if ($existing) {
                $this->addFlash("error", "Le nom existe déjà, veuillez en choisir un autre.");
            } else {
                $prod->setRecycledAt(new \DateTimeImmutable());
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
                        $prod->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                    $this->getUser()->setScore($this->getUser()->getScore() + 10);

                    $prod->setPointderecyclage($pointRecyclage);
                    $prod->setUser($user);
                    $this->em->persist($prod);
                    $this->em->flush();
                    $this->addFlash("message", "produit ajoutée avec succès.");
                    return $this->redirectToRoute('app_profilee', ['id' => $this->getUser()->getId()]);
                }
            }
        }
        return $this->render('frontoffice/pointrecyclage/addProduitfront.html.twig', [
            "form" => $form->createView(),
            "action" => "add",
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/addProduit', name: 'add_produit', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $prod = new Produitrecyclage();
        $form = $this->createForm(ProduitrecyclageType::class, $prod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->em->getRepository(Produitrecyclage::class)->findOneBy(['name' => $prod->getName()]);

            if ($existing) {
                $this->addFlash("error", "Le nom existe déjà, veuillez en choisir un autre.");
            } else {
                $prod->setRecycledAt(new \DateTimeImmutable());
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
                        $prod->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                    }
                    $this->em->persist($prod);
                    $this->em->flush();
                    $this->addFlash("message", "produit ajoutée avec succès.");
                    return $this->redirectToRoute('display_produit');
                }
            }

            return $this->render('backOffice/pointrecyclage/addProduit.html.twig', [
                "form" => $form->createView()
            ]);
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit_produit/{id}', name: 'edit_produit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produitrecyclage $prod): Response
    {
        if (!$prod) {
            throw $this->createNotFoundException('La gategorie n existe pas.');
        }
        $form = $this->createForm(ProduitrecyclageType::class, $prod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->em->getRepository(Produitrecyclage::class)->findOneBy(['name' => $prod->getName()]);

            if ($existing && $existing->getId() !== $prod->getId()) {
                $this->addFlash("error", "Le nom existe déjà, veuillez en choisir un autre.");
            } else {
                $this->em->persist($prod);
                $this->em->flush();
                return $this->redirectToRoute('display_produit');
            }
        }
        return $this->render('backOffice/pointrecyclage/addProduit.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete_produit/{id}', name: 'delete_produit', methods: ['POST', 'GET'])]
    public function delete($id, ProduitrecyclageRepository $produitrecyclageRepository): Response
    {
        $prod = $produitrecyclageRepository->find($id);
        if (!$prod) {
            $this->addFlash("error", "Catégorie introuvable.");
            return $this->redirectToRoute('display_produit');
        }



        $this->em->remove($prod);
        $this->em->flush();

        $this->addFlash("message", "Produit supprimée avec succès.");
        return $this->redirectToRoute('display_produit');
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/produit', name: 'display_produit')]
    public function display(ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $prod = $em->getRepository(Produitrecyclage::class)->findAll();
        return $this->render('backOffice/pointrecyclage/listproduit.html.twig', [
            'produits' => $prod,
        ]);
    }
}
