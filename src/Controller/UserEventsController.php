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
use App\Repository\CategoryRepository;
use App\Service\EventCategoryService;
use App\Service\EventService;
use Symfony\Component\HttpFoundation\Request;

final class UserEventsController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/my-events', name: 'app_my_events')]
    public function events(EventRepository $eventRepository, EventService $eventService,
    CategoryRepository $categoryRepository,
    EventCategoryService $eventCategoryService,): Response
    {

        // Filtrer les événements
        $events_validate = $eventRepository->getAllEventsFromUserWithStatus(1, StatusEnum::VALIDATED);
        $events_awaiting = $eventRepository->getAllEventsFromUserWithStatus(1, StatusEnum::AWAITING_VALIDATION);
        $events_partial_refuse = $eventRepository->getAllEventsFromUserWithStatus(1, StatusEnum::PARTIAL_REFUSED);
        $events_total_refuse = $eventRepository->getAllEventsFromUserWithStatus(1, StatusEnum::TOTAL_REFUSED);
        
        $events = $eventService->getAllWithStatusValidated();
        $categories = $categoryRepository->getCategories();

        $eventCategoryMap = []; // Used to get marker colors and icons
        foreach ($events_validate as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }
        foreach ($events_awaiting as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }
        foreach ($events_partial_refuse as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }
        foreach ($events_total_refuse as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }

        $markerColors = $this->getMarkerColors($eventCategoryService, $eventCategoryMap);
        $icons = $this->getIcons($eventCategoryService, $eventCategoryMap);
        

        return $this->render('user_events/user_events.html.twig', [
            'controller_name' => 'AdminController',
            'events' => [
            [
                "nom" => "Evènements publiés",
                "events" => $events_validate,
                "background" => "bg-validate"
            ],
            [
                "nom" => "Evènements en attente",
                "events" => $events_awaiting,
                "background" => "bg-awaiting"
            ],
            [
                "nom" => "Evènements à reconsidérer",
                "events" => $events_partial_refuse,
                "background" => "bg-partialrefuse"
            ],
            [
                "nom" => "Evènements refusés",
                "events" => $events_total_refuse,
                "background" => "bg-totalrefuse"
            ]
        ],
            'icons' => $icons,
            'markerColors' => $markerColors,

        ]);
    }

    public function getMarkerColors(EventCategoryService $eventCategoryService, array $eventCategoryMap): array
    {
        $markerColors = $eventCategoryService->getMarkerColors($eventCategoryMap);
        return $markerColors;
    }

    // Get the icons for the events shown on the map & card
    public function getIcons(EventCategoryService $eventCategoryService, array $eventCategoryMap): array
    {
        $icons = $eventCategoryService->getIcons($eventCategoryMap);
        return $icons;
    }
}
