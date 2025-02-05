<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Type; // Use correct namespace
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TypesType;
use Symfony\Component\HttpFoundation\JsonResponse;

final class TypesController extends AbstractController
{
    #[Route('/types', name: 'app_types_list')]
    public function index(TypeRepository $typeRepository): Response
    {
        return $this->render('recyclage_point/index.html.twig', [
            'types' => $typeRepository->findAll(),
        ]);
    }

    #[Route('/types/new', name: 'app_type_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = new Type();
        $form = $this->createForm(TypesType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('app_recyclage_point_index');
        }

        return $this->redirectToRoute('app_recyclage_point_index');
    }

    #[Route('/types/edit/{id}', name: 'app_type_edit', methods: ['POST'])]
    public function edit(Request $request, Type $type, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypesType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_recyclage_point_index');
        }

        return $this->redirectToRoute('app_recyclage_point_index');
    }

    #[Route('/types/delete/{id}', name: 'app_type_delete', methods: ['POST'])]
    public function delete(Request $request, Type $type, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            $entityManager->remove($type);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recyclage_point_index');
    }
}
