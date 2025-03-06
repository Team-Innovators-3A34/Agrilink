<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VRRoomController extends AbstractController
{
 
    #[Route('/vr_room/testtheni', name: 'vr_room', methods: ['GET', 'POST'])]   
    
    public function index(): Response
    {
        $frameUrl = "https://framevr.io/testtheni";

        return $this->render('frontoffice/vr_room/index.html.twig', [
            'frameUrl' => $frameUrl,
        ]);
    }
}
