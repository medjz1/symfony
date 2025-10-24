<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/nbEvent', name: 'app_detailEvent')]
    public function isEnable(EventRepository $eventRepo): Response
    {
        $events=$eventRepo->findAll();
        return $this->render('event/event.html.twig',[
            "event"=>$events,
        ]);
    }
}