<?php

namespace App\Service;
use App\Entity\Subject;
use App\Form\CreateEventType;
use App\Form\CreateSubjectType;
use App\Service\PaginatorService;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Events;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function dd;

class EventService
{
    public function __construct(
        private PaginatorService $paginatorService,
        private EventsRepository $eventRepository, private FormFactoryInterface $formFactory,private SluggerInterface $slugger,
        private EntityManagerInterface $em, private ValidatorInterface $validator
    ){}


    public function events(Request $request)
    {
        return $this->paginatorService->paginate($this->eventRepository->findAll(),$request);

    }

    public function create(User $user = null, Request $request)
    {
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $event = (new Events())
            ->setCreatedBy($user)
            ->setStatus('In progress');

        $form = $this->formFactory->create(CreateEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les sujets sélectionnés depuis le formulaire
            $selectedSubjects = $form->get('subjects')->getData();

            // Ajouter chaque sujet à l'événement
            foreach ($selectedSubjects as $subject) {
                $event->addSubject($subject);
            }

            // Générer le slug
            $event->setSlug($this->slugger->slug($event->getTitle()));

            // Persister l'événement et les sujets
            $this->em->persist($event);

            foreach ($selectedSubjects as $subject) {
                // probleme dans le add  => requieert set @ask @arnaud //
                /** @var Subject $subject */
                $subject->setEvents($event);
                $this->em->persist($subject);
            }

            $this->em->flush();

            // Vérifier les sujets après flush
           // dd($event->getSubjects());

            return true;
        }

        return ['form' => $form->createView(), 'event' => $event, 'mode'=>'create'];
    }

    public function remove(Events $event): bool
    {
        $this->eventRepository->remove($event);
        return true;
    }


    public function edit (Events $event= null , Request $request)
    {
        if (!$event) {
            throw new NotFoundHttpException('Evenement  Introuvable');
        }

        $form = $this->formFactory->create(CreateEventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les sujets sélectionnés depuis le formulaire
            $selectedSubjects = $form->get('subjects')->getData();

            // Ajouter chaque sujet à l'événement
            foreach ($selectedSubjects as $subject) {
                $event->addSubject($subject);
            }

            // Générer le slug
            $event->setSlug($this->slugger->slug($event->getTitle()));

            // Persister l'événement et les sujets
            $this->em->persist($event);

            foreach ($selectedSubjects as $subject) {
                // probleme dans le add  => requieert set @ask @arnaud //
                /** @var Subject $subject */
                $subject->setEvents($event);
                $this->em->persist($subject);
            }

            $this->em->flush();

            // Vérifier les sujets après flush
            // dd($event->getSubjects());

            return true;
        }

        return ['form' => $form->createView(), 'event' => $event, 'mode'=>'edit'];
    }


}