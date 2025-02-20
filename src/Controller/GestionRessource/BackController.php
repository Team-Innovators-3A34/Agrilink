<?php

namespace App\Controller\GestionRessource;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Ressources;
use App\Repository\RessourcesRepository;
use App\Entity\Demandes;
use App\Repository\DemandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BackController extends AbstractController
{
    #[Route('/back', name: 'app_back')]
    public function index(): Response
    {
        return $this->render('back/listResources.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/resource', name: 'app_resourceback')]
    public function show(Request $request, RessourcesRepository $ressourceRepository): Response
    {
        $ownerId = $request->query->get('owner_id_id');  // ID du propriétaire
        $type = $request->query->get('type');  // Type de ressource
        if ($ownerId && $type) {
            $ressources = $ressourceRepository->findByOwnerAndType($ownerId, $type);
        } else {
            $ressources = $ressourceRepository->findAll();
        }

        return $this->render('backoffice/ressource/listRessources.html.twig', [
            'ressources' => $ressources,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/demande', name: 'app_demandeback')]
    public function showdemande(DemandesRepository $demandeRepository, Request $request): Response
    {
        $idR = $request->query->get('idR');

        if ($idR) {
            $demandes = $demandeRepository->findByIdR($idR);
        } else {
            $demandes = $demandeRepository->findAll();
        }

        return $this->render('backoffice/ressource/listDemandes.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/demandesback/delete/{id}', name: 'app_demandesdelete', methods: ['POST'])]
    public function delete(Demandes $demande, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($demande);
        $entityManager->flush();

        return $this->redirectToRoute('app_demandeback');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/supprimerr/{id}', name: 'app_resourcedelete', methods: ['POST'])]
    public function deleteresource(int $id, EntityManagerInterface $entityManager): Response
    {
        $ressource = $entityManager->getRepository(Ressources::class)->find($id);

        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        $entityManager->remove($ressource);
        $entityManager->flush();

        $this->addFlash('success', 'Ressource supprimée avec succès.');

        return $this->redirectToRoute('app_resourceback');
    }
}
