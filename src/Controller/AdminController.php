<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Entity\User;
use App\Entity\Role;
use App\Enum\StatusEnum;
use App\Repository\EventStatusRepository;
use App\Repository\RoleRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/events', name: 'app_admin_events')]
    public function events(Request $request, EventRepository $eventRepository): Response
    {
        // Au cas où on veut rajouter un filtre de recherche
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', 2);

        // Filtrer les événements
        $events = $eventRepository->getAllWithFilters($search, $status);

        return $this->render('admin/events.html.twig', [
            'controller_name' => 'AdminController',
            'events' => $events,
            'status' => $status
            /* 'search' => $search */
            
        ]);
    }

    #[Route('/admin/event-to-refuse/{id}', name: 'app_admin_event_to_refuse')]
    public function eventsToRefuse(Request $request, int $id, EventRepository $eventRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $events = $eventRepository->find($id);
        $comment = '';

        // Vérifier si le formulaire est soumis
        if ($request->isMethod('POST')) {
            $comment = $request->request->get('comment');
            $statusId = $request->request->get('statusId');
            $status = $statusRepository->find($statusId);

            $events->getEventStatus()->setStatus($status);
            $events->getEventStatus()->setComment($comment);
            $events->getEventStatus()->setUpdatedAt(new \DateTimeImmutable());
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_admin_events');
        }

        return $this->render('admin/event-to-refuse.html.twig', [
            'controller_name' => 'AdminController',
            'event' => $events,
            'comment' => $comment,
        ]);
    }

    #[Route('/admin/event-to-validate/{id}', name: 'app_admin_event_to_validate')]
    public function eventsToValidate(int $id, EventRepository $eventRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $events = $eventRepository->find($id);
        $status = $statusRepository->find(StatusEnum::VALIDATED);
        $events->getEventStatus()->setStatus($status);
        $events->getEventStatus()->setUpdatedAt(new \DateTimeImmutable());
        $entityManagerInterface->flush();

        return $this->redirectToRoute('app_admin_events');
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepositery, RoleRepository $roleRepository): Response
    {
        $users = $userRepositery->getAll();
        $roles = $roleRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
            'roles' => $roles
        ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'app_admin_user_delete')]
    public function userDelete(int $id,UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            #TODO : Remplacer par une notif utilisateur
            throw new \Exception("Le compte est introuvable");
        }
        $userRepository->delete($user);

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/admin/update-role', name: 'admin_update_role', methods: ['POST'])]
    public function updateRole(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'], $data['newRoleId'])) {
            return new JsonResponse(['success' => false, 'message' => 'Données invalides'], 400);
        }

        $user = $entityManager->getRepository(User::class)->find($data['userId']);
        $role = $entityManager->getRepository(Role::class)->find($data['newRoleId']);

        if (!$user || !$role) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur ou rôle introuvable'], 404);
        }

        $user->setRole($role);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Rôle mis à jour']);
    }
}
