<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Log;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Services\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    private $em;
    private $eventRepo;
    private $eventService;


    public function __construct(EventService $eventService,EntityManagerInterface $em,EventRepository $eventRepo) {
        $this->em = $em;
        $this->eventRepo = $eventRepo;
        $this->eventService = $eventService;
    }

    #[Route('/events', name: 'get_events', methods: ['GET'])]
    public function index():JsonResponse
    {
     $events = $this->eventRepo->findAll();
     return $this->json(['data'=>$events],200);
    }

    #[Route('/events/create', name: 'create_event', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        $result = $this->eventService->createEvent($request, $user);

        if ($result['success']) {
            return $this->json(['data' => 'Event Created'], 201);
        } else {
            return $this->json(['errors' => $result['errors']], 400);
        }
    }

    #[Route('/events/{id}', name: 'show_event', methods: ['GET'])]
    public function show($id):JsonResponse
    {
     $event = $this->eventRepo->find($id);
     if (!$event) {
        return $this->json(['error' => 'Event not found'], 404);
    }
    
     return $this->json(['data'=>$event]);
    }


    #[Route('/events/{id}/edit', name: 'edit_event', methods: ['PUT', 'PATCH'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        $event = $this->eventRepo->find($id);
        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }

        $result = $this->eventService->editEvent($event, $request);

        if ($result['success']) {
            return $this->json(['data' => 'Event Updated'], 200);
        } else {
            return $this->json(['errors' => $result['errors']], 400);
        }
    }

    #[Route('/events/{id}/delete', name: 'delete_event', methods: ['GET','DELETE'])]
    public function delete($id):JsonResponse
    {
        $user = $this->getUser();

     if (!$user) {
        return $this->json(['error' => 'Unauthorized'], 401);
            }
     $event = $this->eventRepo->find($id);
     if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }
     $this->em->remove($event);
     $this->em->flush();
     return $this->json(['message'=>'Event Removed']);
    }


}
