<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EventFormType;
use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\EventService;

final class EventSubmitController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private EventService $eventService;
    public function __construct(HttpClientInterface $httpClient, EventService $eventService)
    {
        $this->httpClient = $httpClient;
        $this->eventService = $eventService;
    }

    #[Route('/event/submit', name: 'event_submit')]
    public function new_event(EventRepository $eventRepository, Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventService->save($event);
            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('event_submit/newEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/getaddress', name: 'get_address', methods: ['GET'])]
    public function get_address(Request $request){
        
        $query = $request->query->get('q');

        if (!$query) {
            return new JsonResponse(['error' => 'No query'], 400);
        }

        // Appel à l'API Nominatim
        $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
            'query' => [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
            ],
            'headers' => [
                'User-Agent' => 'SpotLink' // Obligatoire pour Nominatim
            ]
        ]);
        $data = $response->toArray();
        $results = array_map(fn($item) => [
            'id' => $item['display_name'],
            'text' => $item['display_name'],
            'details' => $item['address']
        ], $data);

        return new JsonResponse(['results' => $results]);
    }

}
