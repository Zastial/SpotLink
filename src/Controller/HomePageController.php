<?php

namespace App\Controller;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Service\EventCategoryService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(
        EventRepository $eventRepository,
        EventCategoryService $eventCategoryService,
        SerializerInterface $serializer
    ): Response
    {
        $events = $eventRepository->getEvents();
        
        // Sérialiser les événements pour JavaScript
        $eventsForJS = array_map(function($event) {
            return [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'latitude' => $event->getLatitude(),
                'longitude' => $event->getLongitude(),
                'created_at' => $event->getCreatedAt()->format('Y-m-d H:i:s'),
                'creator' => $event->getCreator(),
                'status' => $event->getEventStatus()?->getStatus(),
            ];
        }, $events);

        $eventCategoryMap = [];
        foreach ($events as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }
        $markerColors = $this->getMarkerColors($eventCategoryService, $eventCategoryMap);

        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'events' => $events,
            'eventsForJS' => $eventsForJS,
            'markerColors' => $markerColors,
        ]);
    }

    public function getMarkerColors(EventCategoryService $eventCategoryService, array $eventCategoryMap): array
    {
        $markerColors = $eventCategoryService->getMarkerColors($eventCategoryMap);
        return $markerColors;
    }
}
