<?php

namespace App\Services;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service de gestion des événements.
 */
class EventService
{
    private EntityManagerInterface $entityManager;
    private EventRepository $eventRepository;

    public function __construct(EntityManagerInterface $entityManager, EventRepository $eventRepository)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Enregistrer un nouvel événement.
     * @param Event $event L'événement à enregistrer.
     */
    public function save(Event $event): void
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    /**
     * Récupération de l'événement à partir d'une date.
     */
    public function getEventsByDate(\DateTime $date)
    {
        return $this->eventRepository->findEventsByDate($date);
    }
}
