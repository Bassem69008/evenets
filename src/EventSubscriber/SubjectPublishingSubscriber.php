<?php

namespace App\EventSubscriber;

use App\Entity\Subject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\WorkflowInterface;

class SubjectPublishingSubscriber implements EventSubscriberInterface
{

    public function __construct( private Security $security, private  WorkflowInterface $subjectPublishingStateMachine){}


    public function handleReview(TransitionEvent $event): void
    {
        /** @var Subject $subject */
        $subject = $event->getSubject();
        //dd();
    }



    public function guardPublish(GuardEvent $event, ResponseEvent $response)
    {
      if(!$this->security->isGranted(['ROLE_BOARD']))
      {

          $event->setBlocked(true,'unauthorized');
      }

        /** @var Subject $subject */
        $subject = $event->getSubject();
        $this->subjectPublishingStateMachine->apply($subject, 'to_review');
        $this->subjectPublishingStateMachine->can($subject,'publish');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            //'workflow.subject_publishing.transition.to_review'=> 'handleReview',
            'workflow.subject_publishing.guard.to_review'=> ['guardPublish']
        ];
    }
}
