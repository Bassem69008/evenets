<?php

namespace App\Service;

use App\Entity\Subject;
use App\Entity\User;
use App\Form\CreateSubjectType;
use App\Repository\SubjectRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class SubjectService
{
    public function __construct(
        private WorkflowInterface $subjectPublishing,
        private SubjectRepository $subjectRepository,
        private FormFactoryInterface $formFactory,
        private SluggerInterface $slugger
    ) {
    }

    public function show(Subject $subject = null): ?Subject
    {
        if (!$subject) {
            throw new NotFoundHttpException('Sujet non trouvé');
        }
        $this->subjectPublishing->can($subject, Subject::PUBLISH_TRANSITION);

        return $subject;
    }

    public function create(Request $request, User $owner)
    {
        $subject = (new Subject())
             ->setOwnerId($owner)
             ->setStatus('draft');

        $form = $this->formFactory->create(CreateSubjectType::class, $subject);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setSlug($this->slugger->slug($subject->getTitle()));
            $this->subjectRepository->save($subject);

            return true;
        }

        return ['form' => $form->createView(), 'user' => $owner];
    }

    public function edit(Subject $subject = null, Request $request)
    {
        if (!$subject) {
            throw new NotFoundHttpException('Sujet Introuvable');
        }

        $form = $this->formFactory->create(CreateSubjectType::class, $subject);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setSlug($this->slugger->slug($subject->getTitle()));
            $this->subjectRepository->save($subject);

            return true;
        }

        return ['form' => $form->createView(), 'subject' => $subject];
    }

    public function review(Subject $subject = null, string $state = null): ?Subject
    {
        if (!$subject) {
            throw new NotFoundHttpException('Sujet non trouvé');
        }
        $this->subjectPublishing->can($subject, Subject::PUBLISH_TRANSITION);
        $this->subjectPublishing->can($subject, Subject::REJECT_TRANSITION);

        switch ($state) {
            case 'publish':
                $subject->setStatus(Subject::STATUS_PUBLISHED);
                break;
            case 'reject':
                $subject->setStatus(Subject::STATUS_DRAFT);
                break;
            default:
                throw new NotFoundHttpException('unknownState');
        }

        $this->subjectRepository->save($subject);

        return $subject;
    }
}
