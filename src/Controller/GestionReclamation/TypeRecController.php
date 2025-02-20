<?php

namespace App\Controller\GestionReclamation;

use App\Entity\TypeRec;
use App\Form\TypeRecType;
use App\Repository\TypeRecRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/type/rec')]
final class TypeRecController extends AbstractController
{
    #[Route(name: 'app_type_rec_index', methods: ['GET'])]
    public function index(TypeRecRepository $typeRecRepository): Response
    {
        return $this->render('backoffice/type_rec/index.html.twig', [
            'type_recs' => $typeRecRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_rec_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeRec = new TypeRec();
        $form = $this->createForm(TypeRecType::class, $typeRec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeRec);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_rec_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/type_rec/new.html.twig', [
            'type_rec' => $typeRec,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_rec_show', methods: ['GET'])]
    public function show(TypeRec $typeRec): Response
    {
        return $this->render('backoffice/type_rec/show.html.twig', [
            'type_rec' => $typeRec,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_rec_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeRec $typeRec, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeRecType::class, $typeRec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_rec_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/type_rec/edit.html.twig', [
            'type_rec' => $typeRec,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_type_rec_delete', methods: ['POST'])]
    public function delete(Request $request, TypeRec $typeRec, EntityManagerInterface $entityManager): Response
    {
        
            $entityManager->remove($typeRec);
            $entityManager->flush();
        

        return $this->redirectToRoute('app_type_rec_index', [], Response::HTTP_SEE_OTHER);
    }
}




