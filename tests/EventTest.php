<?php

namespace App\Tests;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Services\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class EventTest extends WebTestCase
{
      
    private $formFactory;
    private $entityManager;
    private $eventRepository;
    private $eventService;

    protected static function getKernelClass(): string
    {
        // Return the fully-qualified class name of your Symfony kernel
        return \App\Kernel::class;
    }

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->eventRepository = $this->createMock(EventRepository::class);

        $this->eventService = new EventService($this->entityManager, $this->formFactory, $this->eventRepository);
    }

    public function testCreateEventSuccess()
    {
        
        $user = $this->createMock(User::class);
    
        $event = new Event();
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('handleRequest')
            ->willReturnSelf();
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);
        $form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        $form->expects($this->once())
            ->method('getData')
            ->willReturn($event);
    
        $this->formFactory->expects($this->once())
            ->method('create')
            ->with(EventFormType::class, $event)
            ->willReturn($form);
    
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($event);
        $this->entityManager->expects($this->once())
            ->method('flush');
    
       
        $request = new Request([], [
            'title' => 'Fake Title',
            'description' => 'Fake Description'
        ]);
    
       
        $response = $this->eventService->createEvent($request, $user);
    
       
        $this->assertEquals(['success' => true], $response);
    
        
        $this->assertSame($user, $event->getUser());
    }
}
