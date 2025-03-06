<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoiceController extends AbstractController
{
    #[Route('/voice-command', name: 'voice_command', methods: ['POST'])]
    public function handleVoiceCommand(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = strtolower($data['command'] ?? '');

        switch ($command) {
            case 'home':
            case 'main':
            case 'principal':
            case 'fil':
            case 'actualite':
                return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/home']);
            case 'badge':
                case 'badges':
                return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/badges']);
                case 'notification':
                    case 'notifications':
                    case 'notif':

                    return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/notifications']);
                    case 'point':
                        case 'recyclage':
                            case 'recycle':
                                case 'pointderecyclage':
                                    case 'point de recyclage':
                        return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/Point']);
                        case 'event':
                            case 'events':
                            case 'evenement':
                                case 'evenements':
                            return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/Event']);
                            case 'settings':
                                case 'parametre':
                                    case 'parametres':
                                        case 'setting':
                                return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/settings']);
                                case 'tiktok':
                                    case 'tiktoks':
                                        case 'reels':
                                            case 'videos':
                                                case 'video':
                                            case 'reel':
                                            return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/tiktok']);
                                            case 'features':
                                                case 'feature':
                                                return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/features']);
                                                case 'vr':
                                                    case 'about':
                                                        case 'room':
                                                            case 'realite':
                                                                case 'virtuelle':
                                                    return new JsonResponse(['message' => 'Opening ...', 'redirect' => '/vr_room/testtheni']);
            default:
                return new JsonResponse(['message' => 'Unknown command! Try "Open Dashboard" or "Show Products".']);
        }
    }
}
