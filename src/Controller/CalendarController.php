<?php
// src/Controller/CalendarController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Event;

class CalendarController extends AbstractController
{
    #[Route('/api/events', name: 'api_events')]
    public function getEvents(EntityManagerInterface $em): JsonResponse
    {
        $events = $em->getRepository(Event::class)->findAll();
        $data = [];

        foreach ($events as $event) {
            $data[] = [
                'title' => $event->getTitle(),
                'start' => $event->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $event->getEndDate()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/calendar', name: 'calendar')]
    public function index(): Response
    {
        return $this->render('calendar.html.twig');
    }
}
