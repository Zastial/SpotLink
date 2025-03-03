<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\EventStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EventFormType;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Repository\EventRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\EventService;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\StatusEnum;

final class EventSubmitController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private EventService $eventService;
    private EntityManagerInterface $entityManager;
    public function __construct(HttpClientInterface $httpClient, EventService $eventService, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->eventService = $eventService;
        $this->entityManager = $entityManager;
    }

    #[Route('/event/submit', name: 'event_submit')]
    public function new_event(Request $request, UserRepository $userRepository, StatusRepository $statusRepository, EventStatusRepository $eventStatusRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->beginTransaction();
            try{
                //ToDo: à remplacer par le user connecté
                $user = $userRepository->findOneBy([], ['id' => 'ASC']);
                $event->setCreator($user);

                $event->setCreatedAt(new \DateTimeImmutable());
                $this->eventService->save($event);

                // Création du statut de l'événement
                $eventStatus = new EventStatus();
                $eventStatus->setEvent($event);
                $eventStatus->setCreatedAt(new \DateTimeImmutable());

                // Assigner le statut "CREATED"
                $status = $statusRepository->find(StatusEnum::AWAITING_VALIDATION);
                $eventStatus->setStatus($status);
                $eventStatusRepository->save($eventStatus);

                $this->entityManager->commit();
                return $this->redirectToRoute('app_home_page');
            }catch (\Exception $e) {
                $this->entityManager->rollback();
                $this->addFlash('error', 'Une erreur est survenue lors de la création de l\'événement : ' . $e->getMessage());

                return $this->redirectToRoute('event_submit');
            }
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
            'osm_id' => $item['osm_id'],
            'lat' => $item['lat'],
            'lon' => $item['lon'],
            'text' => $item['display_name'],
            'details' => $item['address']
        ], $data);

        return new JsonResponse(['results' => $results]);
    }

}
