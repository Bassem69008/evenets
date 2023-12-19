<?php

namespace App\Controller;

use App\Entity\Subject;


use App\Service\SpreadSheet\Event\ExportSubscribersService;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EventService;
use App\Entity\Events;
use Symfony\Component\HttpFoundation\Request;

#[Route('/evennements', name:'events_')]
class EventController extends AbstractController
{
    public function __construct(private EventService $eventService, private ExportSubscribersService $exportSubscribersService){}
    #[Route('', name: 'index')]
    public function index(Request $request): Response
    {
        return $this->render('event/index.html.twig', [
            'pagination'=>$this->eventService->events($request)
        ]);
    }

    #[Route('/{slug}/show', name:'show')]
    public function show(Events $event, Request $request)
    {
        return $this->render('event/show.html.twig',[
            'event' =>$event
        ]);
    }

    #[Route('/creation', name:'create')]
    public function create(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('not allowed');
        }
        $result = $this->eventService->create($this->getUser(), $request);
        if (true === $result) {
            return $this->redirectToRoute('events_index');
        }

        return $this->render('event/add.html.twig', $result);

    }

    #[Route('/{id}/delete', name: 'delete', methods: ['get'])]
    public function delete(Events $event = null, Request $request)
    {

        if (!$event) {
            return $this->render('errors/404.html.twig');
        }
        $result =$this->eventService->remove($event);
        if(true === $result)
        {
            return $this->redirectToRoute('events_index');
        }

        return $this->redirectToRoute('events_index');
    }

    #[Route('/{slug}/edit', name:'edit')]
    public function edit(Events $event= null, Request $request)
    {
        try {
            $result = $this->eventService->edit($event, $request);

            if (true === $result) {
                return $this->redirectToRoute('events_index');
            }

            return $this->render('event/add.html.twig', $result);
        } catch (NotFoundHttpException $e) {
            return $this->render('errors/404.html.twig');
        }
    }

    #[Route('/{slug}/inscription', name: 'subscription')]
    public function subscription(Events $event= null, Request $request)
    {

        $result = $this->eventService->subscription($event, $this->getUser(), $request);
        if(false === $result)
        {
            return $this->render('errors/404.html.twig');
        }
        return $this->render('event/show.html.twig',[
            'event' =>$event
        ]);
    }

    #[Route('/{slug}/desinscription', name: 'unsubscribe')]
    public function unsubscribe(Events $event= null ,Request $request)
    {

        $result = $this->eventService->unsubscribe($event, $this->getUser(), $request);

        if(false === $result)
        {
            return $this->render('errors/404.html.twig');
        }
        return $this->render('event/show.html.twig',[
            'event' =>$event
        ]);
    }

    #[Route('/{slug}', name:'subscribers')]
    public function getSubscribers(Events $event = null, Request $request)
    {
        try {
            $result = $this->eventService->getSubscribers($event, $request);

            return $this->render('event/subscribers.html.twig', \compact('result'));
        } catch (NotFoundHttpException $e) {
            return $this->render('errors/404.html.twig');
        }
    }

    #[Route('/{slug}/export', name:'export_subscribers')]
    public function exportSubscribers(Events $event = null, Request $request)
    {
        try {
            $file = $this->exportSubscribersService->exportSubscribers($event, $request);

            $response = new StreamedResponse();
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$file['filename'].'"');
            $writer = new Csv($file['spreadsheet']);
            $response->setCallback(function() use ($writer) {
                $writer->save('php://output');
            });

            return $response;

        } catch (NotFoundHttpException $e) {
            return $this->render('errors/404.html.twig');
        }
    }


}
