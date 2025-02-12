<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Ressources;
use App\Repository\RessourcesRepository;
use App\Entity\Demandes;
use App\Repository\DemandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
final class BackController extends AbstractController
{
    #[Route('/back', name: 'app_back')]
    public function index(): Response
    {
        return $this->render('back/listResources.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    #[Route('/backresource', name: 'app_resourceback')]
    public function show(RessourcesRepository $ressourceRepository): Response
    {
        // Récupérer toutes les ressources de la base de données
        $ressources = $ressourceRepository->findAll();

        return $this->render('back/listResources.html.twig', [
            'ressources' => $ressources,
        ]);
        
    }
    #[Route('/backdemande', name: 'app_demandeback')]
    public function showdemande(DemandesRepository $demandeRepository ,Request $request): Response
    {
        $idR = $request->query->get('idR');

        if ($idR) {
            $demandes = $demandeRepository->findByIdR($idR);
        } else {
            $demandes = $demandeRepository->findAll();
        }

        return $this->render('back/listDemandes.html.twig', [
            'demandes' => $demandes,
        ]);
        
    }
    #[Route('/demandesback/delete/{id}', name: 'app_demandesdelete', methods: ['POST'])]
public function delete(Demandes $demande, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($demande);
    $entityManager->flush();

    return $this->redirectToRoute('app_demandeback');
}
// Route pour supprimer une ressource
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