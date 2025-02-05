<?php

namespace App\Controller;

use App\Entity\RecyclagePoint;
use App\Entity\Type;
use App\Form\RecyclagePoint1Type;
use App\Form\TypesType;
use App\Repository\RecyclagePointRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recyclage/point')]
final class RecyclagePointController extends AbstractController
{
    #[Route(name: 'app_recyclage_point_index', methods: ['GET'])]
    public function index(RecyclagePointRepository $recyclagePointRepository , TypeRepository $typeRepository): Response
    {
        $recyclagePoints = $recyclagePointRepository->findAll();
       
        $types = $typeRepository->findAll();
        $editForms = [];
    
        foreach ($recyclagePoints as $point) {
            $editForms[$point->getId()] = $this->createForm(RecyclagePoint1Type::class, $point)->createView();
        }
        $editFormstype = [];
        foreach ($types as $type) {
            $editFormstype[$type->getId()] = $this->createForm(TypesType::class, $type)->createView();
        }
        $form = $this->createForm(RecyclagePoint1Type::class, new RecyclagePoint())->createView();
        $form_type = $this->createForm(TypesType::class, new Type())->createView();
        return $this->render('recyclage_point/index.html.twig', [
            'recyclage_points' => $recyclagePoints,
            'types' => $types,
            'edit_forms_type' => $editFormstype,
            'edit_forms' => $editForms,
            'form' => $form,
            'form_type' => $form_type,
        ]);
    }
    #[Route('/new', name: 'app_recyclage_point_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recyclagePoint = new RecyclagePoint();
        $form = $this->createForm(RecyclagePoint1Type::class, $recyclagePoint);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Manually set latitude and longitude from the form input (hidden fields)
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');
            
            if ($latitude && $longitude) {
                $recyclagePoint->setLatitude($latitude);
                $recyclagePoint->setLongitude($longitude);
            }
    
            $entityManager->persist($recyclagePoint);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_recyclage_point_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('recyclage_point/new.html.twig', [
            'recyclage_point' => $recyclagePoint,
            'form' => $form,
        ]);
    }
    
    



    #[Route('/{id}', name: 'app_recyclage_point_show', methods: ['GET'])]
    public function show(RecyclagePoint $recyclagePoint): Response
    {
        return $this->render('recyclage_point/show.html.twig', [
            'recyclage_point' => $recyclagePoint,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recyclage_point_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RecyclagePoint $recyclagePoint, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecyclagePoint1Type::class, $recyclagePoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recyclage_point_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recyclage_point/edit.html.twig', [
            'recyclage_point' => $recyclagePoint,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recyclage_point_delete', methods: ['POST'])]
    public function delete(Request $request, RecyclagePoint $recyclagePoint, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recyclagePoint->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recyclagePoint);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recyclage_point_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/recyclage/point/updatecoordinates', name: 'app_recyclage_point_update_coordinates', methods: ['POST'])]
    public function updateCoordinates(Request $request, RecyclagePoint $recyclagePoint, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['latitude']) || !isset($data['longitude'])) {
            return new JsonResponse(['error' => 'Missing latitude or longitude'], 400);
        }
    
        $recyclagePoint->setLatitude($data['latitude']);
        $recyclagePoint->setLongitude($data['longitude']);
        $entityManager->flush();
    
        return new JsonResponse([
            'message' => 'Coordinates updated successfully',
            'latitude' => $recyclagePoint->getLatitude(),
            'longitude' => $recyclagePoint->getLongitude(),
        ]);
    }
    

}
