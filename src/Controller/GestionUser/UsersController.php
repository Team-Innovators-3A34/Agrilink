<?php

namespace App\Controller\GestionUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailerService;
use App\Repository\UserRepository;
use App\Entity\Friendship;
use App\Entity\Demandes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use phpDocumentor\Reflection\Types\This;
use DateTime;
use App\Service\FriendshipService;
use App\Service\PdfGenerator;


final class UsersController extends AbstractController
{
    private MailerService $mailerService;
    private EntityManagerInterface $em;

    public function __construct(MailerService $mailerService, EntityManagerInterface $em)
    {
        $this->mailerService = $mailerService;
        $this->em = $em;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/users', name: 'app_dashboard_users')]
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findBy(['status' => 'show']);

        return $this->render('backoffice/users/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}', name: 'app_accept_users')]
    public function acceptUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $user->setStatus('approved');
        $this->em->persist($user);
        $this->em->flush();
        $this->mailerService->sendAccountVerificationEmail($user->getEmail(), $user);
        return $this->redirectToRoute("app_dashboard_users");
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/delete/{id}', name: 'app_delete_users')]
    public function deleteUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $user->setStatus('hide');
        $this->em->flush();
        return $this->redirectToRoute("app_dashboard_users");
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/reject/{id}', name: 'app_reject_users')]
    public function rejectUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $user->setStatus('rejected');
        $this->em->persist($user);
        $this->em->flush();
        return $this->redirectToRoute("app_dashboard_users");
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/update/{id}', name: 'app_update_users')]
    public function updateUsers(String $id, Request $request): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $form = $this->createForm(UserType::class, $user, [
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute("app_dashboard_users");
        }

        return $this->render('backoffice/users/updateUsers.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/detail/{id}', name: 'app_detail_users')]
    public function detailUsers(String $id): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);

        return $this->render('backoffice/users/userDetail.html.twig', [
            'user' => $user,
        ]);
    }


    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/profilee/{id}', name: 'app_profilee')]
    public function profile(User $user, FriendshipService $friendshipService): Response
    {
        $friendship = $friendshipService->getFriendship($user);
        $demandes = $friendshipService->updateExpiredRequests();

        return $this->render('frontoffice/profile/profile.html.twig', [
            'user' => $user,
            'friendship' => $friendship,
            'demandes' => $demandes
        ]);
    }

    #[IsGranted('ROLE_AGRICULTURE')]
    #[IsGranted('ROLE_RECYCLING_INVESTOR')]
    #[IsGranted('ROLE_RESOURCE_INVESTOR')]
    #[Route('/profilee/{id}/pdf', name: 'app_profilee_pdf')]
    public function generateProfilePdf(User $user, PdfGenerator $pdfGenerator): Response
    {
        // No need to find user, the param converter does it for you

        // Get current user for personalization
        $currentUser = $this->getUser();

        // Generate and return PDF
        return $pdfGenerator->generateProfilePdf($user, $currentUser);
    }

    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(EntityManagerInterface $em): JsonResponse
    {
        $users = $em->getRepository(User::class)->getAllUsersForMatching();
        return new JsonResponse($users);
    }


    #[Route('/api/matching/{userId}', name: 'get_matching', methods: ['GET'])]
    public function getMatching(int $userId): JsonResponse
    {
        $matches = shell_exec("python3 match.py " . escapeshellarg($userId));
        return new JsonResponse(json_decode($matches, true));
    }

    #[Route('/badges', name: 'app_badge')]
    public function badge(): Response
    {
        return $this->render('frontoffice/badge/badge.html.twig');
    }

    #[Route('/search-users', name: 'search_users')]
    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->query->get('query');

        if (!$query) {
            return new JsonResponse([]);
        }

        $users = $this->em->createQueryBuilder()
            ->select('u.id, u.nom, u.prenom, u.image')
            ->from(User::class, 'u')
            ->where('u.nom LIKE :query OR u.prenom LIKE :query ')
            ->andWhere('u.roles IN (:roles)')
            ->setParameter('query', "%{$query}%")
            ->setParameter('roles', [
                '["ROLE_AGRICULTURE","ROLE_USER"]',
                '["ROLE_RESOURCE_INVESTOR","ROLE_USER"]',
                '["ROLE_RECYCLING_INVESTOR","ROLE_USER"]'
            ])
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return new JsonResponse($users);
    }

    #[Route('/notifications', name: 'app_notification', methods: ['POST', 'GET'])]
    public function notification(): Response
    {


        return $this->render('frontoffice/notification/notification.html.twig', []);
    }

    #[Route('/search-users/badge', name: 'app_search_users')]
    public function searchUsersBadge(Request $request, UserRepository $userRepository): JsonResponse
    {
        $query = $request->query->get('query');

        if ($query === "") {
            // If the search query is empty, return all users
            $users = $userRepository->getAllUsersForMatching();
        } else {
            // Perform the search
            $users = $userRepository->findBySearchQuery($query);
        }

        // Prepare and return the response in JSON format
        $response = [];
        foreach ($users as $user) {
            $response[] = [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'image' => $user->getImage(),
                'score' => $user->getScore(),
                'friendsCount' => $user->getFriendsCount(), // Add the friends count here
                'score' => $user->getScore(),

            ];
        }

        return new JsonResponse($response);
    }

    #[Route('/blankfilter', name: 'get_user', methods: ['GET'])]
    public function blankfilter(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->getAllUsersForMatching();

        $response = [];

        foreach ($users as $user) {
            $response[] = [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'image' => $user->getImage(),
                'score' => $user->getScore(),
                'friendsCount' => $user->getFriendsCount(), // Add the friends count here
                'score' => $user->getScore(),

            ];
        }

        return new JsonResponse($response);
    }


    
}
