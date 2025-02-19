<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
<<<<<<< HEAD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Posts;
use App\Form\PostsType;
use App\Repository\PostsRepository;
=======
use App\Entity\Ressources;
use App\Repository\RessourcesRepository;
use App\Entity\Demandes;
use App\Repository\DemandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
>>>>>>> origin/gestionressources
final class BackController extends AbstractController
{
    #[Route('/back', name: 'app_back')]
    public function index(): Response
    {
<<<<<<< HEAD
        return $this->render('backoffice/base.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    #[Route('/postsback', name: 'app_postsback')]
    public function index1(EntityManagerInterface $entityManager): Response
    {
       
        $posts = $entityManager->getRepository(Posts::class)->findBy(
            [], // no criteria - get all posts
            ['created_at' => 'DESC'] // order by createdAt descending
        );

        // Render the template with the posts
        return $this->render('backoffice/posts.html.twig', [
            'posts' => $posts,
        ]);
    }

}
=======
        return $this->render('back/listResources.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    #[Route('/backresource', name: 'app_resourceback')]
    public function show(Request $request,RessourcesRepository $ressourceRepository): Response
    {
        // Récupérer les paramètres de filtrage
        $ownerId = $request->query->get('owner_id_id');  // ID du propriétaire
        $type = $request->query->get('type');  // Type de ressource
        if($ownerId && $type)
    {  $ressources = $ressourceRepository->findByOwnerAndType($ownerId, $type);
    }else
      { $ressources = $ressourceRepository->findAll();}

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
>>>>>>> origin/gestionressources
