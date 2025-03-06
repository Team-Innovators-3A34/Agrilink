<?php

namespace App\Controller\GestionUser;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NotificationService;
use Symfony\Contracts\HttpClient\HttpClientInterface;



class RegistrationController2 extends AbstractController
{
    private $notificationService;
    private HttpClientInterface $httpClient;

    public function __construct(NotificationService $notificationService , HttpClientInterface $httpClient)
    {
        $this->notificationService = $notificationService;
        $this->httpClient = $httpClient;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userType = $form->get('user_type')->getData();
            $geoData = $this->getGeolocation();

            // Assign a role based on the user type
            switch ($userType) {
                case 'agriculture':
                    $user->setRoles(['ROLE_AGRICULTURE', 'ROLE_USER']);
                    $user->setImage('agriculteur.png');
                    break;
                case 'agricultural_resource_investor':
                    $user->setRoles(['ROLE_RESOURCE_INVESTOR', 'ROLE_USER']);
                    $user->setImage('ressource.png');
                    break;
                case 'recycling_investor':
                    $user->setRoles(['ROLE_RECYCLING_INVESTOR', 'ROLE_USER']);
                    $user->setImage('pointRecyclage.png');
                    break;
            }

            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setStatus('show');
            $user->setAccountVerification('pending');

            $user->setLatitude($geoData['latitude']);
            $user->setLongitude($geoData['longitude']);
            $user->setCity($geoData['city']);
            $user->setCountry($geoData['country']);

            $user->setCreateAt(new \DateTimeImmutable());

            $user->setIs2FA(false);
            $user->setScore(150);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->notificationService->registerNotification($user);

            $this->addFlash('success', 'Votre compte a été créé. Veuillez vous connecter pour que votre compte soit actif.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }

    private function getGeolocation(): ?array
    {
        // Get the public IP
        $response = $this->httpClient->request('GET', 'https://api64.ipify.org?format=json');
        $data = $response->toArray();
        $ip = $data['ip'] ?? null;

        if (!$ip) {
            return null;
        }

        // Get geolocation data
        $geoResponse = $this->httpClient->request('GET', "http://ip-api.com/json/{$ip}");
        $geoData = $geoResponse->toArray();

        if ($geoData['status'] !== 'success') {
            return null;
        }

        return [
            'ip' => $ip,
            'latitude' => $geoData['lat'],
            'longitude' => $geoData['lon'],
            'city' => $geoData['city'],
            'country' => $geoData['country']
        ];
    }
}
