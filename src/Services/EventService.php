<?php

namespace App\Services;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class EventService
{
    private $em;
    private $formFactory;
    private $eventRepo;


    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory,EventRepository $eventRepo)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->eventRepo = $eventRepo;

    }

    public function createEvent(Request $request, User $user): array
    {
        $event = new Event();
        $form = $this->formFactory->create(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setUser($user);
            $this->em->persist($event);
            $this->em->flush();
            return ['success' => true];
        }

        return ['success' => false, 'errors' => $this->getFormErrors($form)];
    }

    public function editEvent(Event $event, Request $request): array
    {
        $form = $this->formFactory->create(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventRepo->setTitle($form->get('title')->getData());
            $this->eventRepo->setDescription($form->get('description')->getData());
            $this->em->flush();
            return ['success' => true];
        }

        return ['success' => false, 'errors' => $this->getFormErrors($form)];
    }

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true, false) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }
}
