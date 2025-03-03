<?php

namespace App\Controller;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Services\EventCategoryService;
use App\Services\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'redirect_home_page')]
    public function redirectToHomePage(): Response
    {
        return $this->redirectToRoute('app_home_page');
    }

    // The route for the home page
    #[Route('/home', name: 'app_home_page')]
    public function index(
        EventService $eventService,
        CategoryRepository $categoryRepository,
        EventCategoryService $eventCategoryService,
        SerializerInterface $serializer
    ): Response
    {
        $events = $eventService->getAllWithStatusValidated();
        $categories = $categoryRepository->getCategories();

        $eventCategoryMap = []; // Used to get marker colors and icons
        foreach ($events as $event) {
            $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        }
        $markerColors = $this->getMarkerColors($eventCategoryService, $eventCategoryMap);
        $icons = $this->getIcons($eventCategoryService, $eventCategoryMap);

        usort($events, function($a, $b) {
            return $a->getDateStart() <=> $b->getDateStart();
        }); // Sort events by date to show the closest first

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
            'events' => $events,
            'eventsForJS' => $eventsForJS,
            'markerColors' => $markerColors,
            'icons' => $icons,
            'categories' => $categories,
        ]);
    }

    // Get the marker colors for the events shown on the map
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

    // The route for the detail page of an event
    #[Route('/event/{id}', name: 'event_detail')]
    public function show(
        EventRepository $eventRepository,
        CategoryRepository $categoryRepository,
        EventCategoryService $eventCategoryService,
        int $id
    ): Response
    {
        $event = $eventRepository->find($id);
        $eventForJS = [
            'id' => $event->getId(),
            'name' => $event->getName(),
            'description' => $event->getDescription(),
            'latitude' => $event->getLatitude(),
            'longitude' => $event->getLongitude(),
            'location_name' => $event->getLocationName(),
            'house_number' => $event->getHouseNumber(),
            'road' => $event->getRoad(),
            'postcode' => $event->getPostcode(),
            'city' => $event->getCity(),
            'county' => $event->getCounty(),
            'state' => $event->getState(),
            'country' => $event->getCountry(),
            'created_at' => $event->getCreatedAt()->format('Y-m-d H:i:s'),
            'creator' => $event->getCreator(),
            'status' => $event->getEventStatus()?->getStatus(),
            'category_name' => $event->getCategory()->getName(),
            'date_start' => $event->getDateStart()?->format('Y-m-d H:i:s'),
            'date_end' => $event->getDateEnd()?->format('Y-m-d H:i:s'),
        ];
        $categories = $categoryRepository->getCategories();
        $eventCategoryMap[$event->getId()] = $event->getCategory()->getName();
        $markerColors = $this->getMarkerColors($eventCategoryService, $eventCategoryMap);
        $icons = $this->getIcons($eventCategoryService, $eventCategoryMap);

        return $this->render('event/detail.html.twig', [
            'event' => $event,
            'eventForJS' => $eventForJS,
            'markerColors' => $markerColors,
            'icons' => $icons,
            'categories' => $categories,
        ]);
    }
}
