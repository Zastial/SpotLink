<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EventFormType;
use App\Entity\Event;
use App\Repository\EventRepository;

final class EventSubmitController extends AbstractController
{
    #[Route('/event/submit', name: 'event_submit')]
    public function new_event(EventRepository $eventRepository, Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        return $this->render('event_submit/newEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
