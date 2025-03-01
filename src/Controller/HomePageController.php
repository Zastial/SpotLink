<?php

namespace App\Controller;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Services\EventCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(
        EventRepository $eventRepository,
        CategoryRepository $categoryRepository,
        EventCategoryService $eventCategoryService,
        SerializerInterface $serializer
    ): Response
    {
        $events = $eventRepository->getAllWithStatusValidated();
        $categories = $categoryRepository->getCategories();

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
                'category' => $event->getCategory(),
                'date_start' => $event->getDateStart()?->format('Y-m-d H:i:s'),
            ];
        }, $events);

        $eventCategoryMap = [];
        foreach ($events as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }
        $markerColors = $this->getMarkerColors($eventCategoryService, $eventCategoryMap);

        usort($events, function($a, $b) {
            return $a->getDateStart() <=> $b->getDateStart();
        });

        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'events' => $events,
            'eventsForJS' => $eventsForJS,
            'markerColors' => $markerColors,
            'categories' => $categories,
        ]);
    }

    public function getMarkerColors(EventCategoryService $eventCategoryService, array $eventCategoryMap): array
    {
        $markerColors = $eventCategoryService->getMarkerColors($eventCategoryMap);
        return $markerColors;
    }
}
