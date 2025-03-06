<?php

namespace App\Controller\GestionUser;

use App\Entity\Event;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Posts;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\WeatherService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, WeatherService $weatherService): Response
    {

        $city = $request->query->get('city', 'Tunis'); // Ville par défaut
        $weatherData = $weatherService->getWeather($city);

        $posts = $this->em->getRepository(Posts::class)->findAll();
        shuffle($posts);

        $events = $this->em->getRepository(Event::class)->findAll();

        return $this->render('frontoffice/homePage/homePage.html.twig', [
            'posts' => $posts,
            'events' => $events,
            'weather' => $weatherData,
            'city' => $city
        ]);
    }

    #[Route('/get-weather', name: 'get_weather', methods: ['POST'])]
public function getWeather(Request $request,WeatherService $weatherService): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $city = $data['city'] ?? null;

    if (!$city) {
        return $this->json(["error" => "Ville requise"], 400);
    }

    $weatherData = $weatherService->getWeather($city);


    // Format response to match your expected JSON format
    return $this->json([
        "temperature" => $weatherData["main"]["temp"],
        "humidity" => $weatherData["main"]["humidity"],
        "description" => $weatherData["weather"][0]["description"],
        "wind" => $weatherData["wind"]["speed"]
    ]);
}

}
