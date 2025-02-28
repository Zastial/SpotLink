<?php

namespace App\Controller;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Service\EventCategoryService;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(
        EventRepository $eventRepository,
        EventService $eventService,
        CategoryRepository $categoryRepository,
        EventCategoryService $eventCategoryService,
        SerializerInterface $serializer
    ): Response
    {
        $events = $eventService->getAllWithStatusValidated();
        $categories = $categoryRepository->getCategories();

        $eventCategoryMap = [];
        foreach ($events as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }
        $markerColors = $this->getMarkerColors($eventCategoryService, $eventCategoryMap);
        $icons = $this->getIcons($eventCategoryService, $eventCategoryMap);

        usort($events, function($a, $b) {
            return $a->getDateStart() <=> $b->getDateStart();
        });

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
                'category_name' => $event->getCategory()->getName(),
                'date_start' => $event->getDateStart()?->format('Y-m-d H:i:s'),
            ];
        }, $events);

        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'events' => $events,
            'eventsForJS' => $eventsForJS,
            'markerColors' => $markerColors,
            'icons' => $icons,
            'categories' => $categories,
        ]);
    }

    public function getMarkerColors(EventCategoryService $eventCategoryService, array $eventCategoryMap): array
    {
        $markerColors = $eventCategoryService->getMarkerColors($eventCategoryMap);
        return $markerColors;
    }

    public function getIcons(EventCategoryService $eventCategoryService, array $eventCategoryMap): array
    {
        $icons = $eventCategoryService->getIcons($eventCategoryMap);
        return $icons;
    }

    #[Route('/event/{id}', name: 'event_detail')]
    public function show(EventRepository $eventRepository, int $id): Response
    {
        $event = $eventRepository->find($id);
        return $this->render('event/detail.html.twig', [
            'event' => $event,
        ]);
    }
}
