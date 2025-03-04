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
use App\Services\EventService;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\StatusEnum;
use App\Services\GetUserInformationService;

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

    #[Route('/create_event/{id?}', name: 'create_event')]
    public function new_event(Request $request, GetUserInformationService $getUserInformationService, UserRepository $userRepository, StatusRepository $statusRepository, EventStatusRepository $eventStatusRepository, EventRepository $eventRepository, ?int $id = null): Response
    {
        $event = new Event();
        $eventStatus = new EventStatus();
        $event_message = "";

        // Récupération de l'événement et de son statut s'ils existent
        $event = $id ? $eventRepository->find($id) : new Event();
        $eventStatus = $id ? $eventStatusRepository->findOneBy(['event' => $event]) : new EventStatus();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $this->entityManager->beginTransaction();
            try {
                $userDto = $getUserInformationService->getUserInformation($request);

                if (!$id) {
                    $user = $userRepository->find($userDto->id);
                    $event->setCreator($user);
                    $event->setCreatedAt(new \DateTimeImmutable());

                    $eventStatus->setEvent($event);
                    $eventStatus->setCreatedAt(new \DateTimeImmutable());

                    $message = "créé";
                } else {
                    if ($event->getCreator()->getId() != $userDto->id) {
                        throw new \Exception("Vous n'êtes pas autorisé à modifier cet événement", 401);
                    }
                    $eventStatus->setComment("");
                    $eventStatus->setUpdatedAt(new \DateTimeImmutable());
                    $message = "modifié";
                }

                // Assigner le statut "AWAITING_VALIDATION" pour les nouveaux événements ou évenements modifiés
                $status = $statusRepository->find(StatusEnum::AWAITING_VALIDATION);
                $eventStatus->setStatus($status);

                $this->eventService->save($event);
                $eventStatusRepository->save($eventStatus);
                $this->entityManager->commit();

                $this->addFlash('success', 'Événement ' . $message . ' avec succès !');
                return $this->redirectToRoute('home');
            } catch (\Exception $e) {
                $this->entityManager->rollback();
                if ($e->getCode() === 401) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->redirectToRoute('home');
                }
                $this->addFlash('error', "Un problème est survenu lors de l'opération");
                return $this->redirectToRoute('event_submit');
            }
        }

        return $this->render('event_submit/newEvent.html.twig', [
            'form' => $form->createView(),
            'event_id' => $id,
        ]);
    }

    #[Route('/getaddress', name: 'get_address', methods: ['GET'])]
    public function get_address(Request $request)
    {

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
