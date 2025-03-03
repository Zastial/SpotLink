<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Enum\StatusEnum;
use App\Repository\EventStatusRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

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
        $status = $request->query->get('status', -1);

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
        $status = $statusRepository->find(StatusEnum::VALIDATED->value);
        $events->getEventStatus()->setStatus($status);
        $events->getEventStatus()->setUpdatedAt(new \DateTimeImmutable());
        $entityManagerInterface->flush();

        return $this->redirectToRoute('app_admin_events');
    }
}
