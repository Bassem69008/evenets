<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\CreateSubjectType;
use App\Repository\CommentRepository;
use App\Repository\SubjectRepository;
use App\Service\Utils\CrudEntityService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Workflow\WorkflowInterface;

class SubjectService
{
    public const SLUG_PROPERTY = 'slug';

    public function __construct(
        private WorkflowInterface $subjectPublishing,
        private SubjectRepository $subjectRepository,
        private FormFactoryInterface $formFactory,
        private CommentRepository $commentRepository,
        private CrudEntityService $entityService,
    ) {
    }

    public function show(Subject $subject = null, User $user = null, Request $request): mixed
    {
        if (!$subject) {
            throw new NotFoundHttpException('Sujet non trouvé');
        }
        $this->subjectPublishing->can($subject, Subject::PUBLISH_TRANSITION);

        $comment = (new Comment())
            ->setUser($user)
            ->setSubjects($subject)
            ->setType(Comment::COMMENT_SUBJECT);
        ;

        return $this->entityService->createOrUpdate($comment, CommentType::class, $request, false, null, $subject);
    }

    public function create(User $owner, Request $request): mixed
    {
        if (!$owner) {
            throw new NotFoundHttpException('user not found');
        }
        $subject = (new Subject())
             ->setOwnerId($owner)
             ->setStatus('draft');

        return $this->entityService->createOrUpdate($subject, CreateSubjectType::class, $request, false, [self::SLUG_PROPERTY]);
    }

    public function edit(Subject $subject = null, Request $request): mixed
    {
        if (!$subject) {
            throw new NotFoundHttpException('Sujet Introuvable');
        }

        return $this->entityService->createOrUpdate($subject, CreateSubjectType::class, $request, false, [self::SLUG_PROPERTY]);
    }

    public function requestReview(Subject $subject = null, Request $request, User $user)
    {
        $this->subjectPublishing->apply($subject,Subject::REVIEWED_TRANSITION);
        $this->subjectRepository->save($subject);
        return true;
    }

    public function review(Subject $subject = null, string $state = null, User $user, Request $request): bool
    {
        if (!$subject) {
            throw new NotFoundHttpException('Sujet non trouvé');
        }



        switch ($state) {
            case 'publish':
                $this->subjectPublishing->apply($subject,Subject::PUBLISH_TRANSITION);
                break;
            case 'reject':
                $this->subjectPublishing->apply($subject,Subject::REJECT_TRANSITION);
                break;
            default:
                throw new NotFoundHttpException('unknownState');
        }

        $this->subjectRepository->save($subject);
        return true;

        //return $comment;
    }

    /*public function createComment(Subject $subject, User $user, Request $request): mixed
    {
        $comment = (new Comment())
            ->setUser($user)
            ->setSubjects($subject)
        ;
        $this->subjectRepository->save($subject);

        $form = $this->formFactory->create(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->save($comment);

            return true;
        }

        return ['form' => $form->createView(), 'subject' => $subject];
    } */
}
