<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Events;
use App\Entity\Subject;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use PHPUnit\Util\Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function settype;

class CommentService
{
    public function __construct(private CommentRepository $commentRepository, private FormFactoryInterface $formFactory)
    {
    }

    public function create(Events $event =null,Subject $subject = null, User $user = null, Comment $parent = null, Request $request, ?string $content, string $type)
    {
        if ( null == $user) {
            throw new BadRequestHttpException('Erreur de soumission');
        }

        $comment = (new Comment())
            ->setUser($user)
            ->settype($type);
       Comment::COMMENT_SUBJECT ? $comment->setSubjects($subject): $comment->setEvent($event);


        if (null !== $parent) {
            return $this->createReply($comment, $parent, $content, $type);
        }

        $form = $this->formFactory->create(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->save($comment);

            return ['success' => true];
        }

        return ['success' => false, 'form' => $form->createView(), 'comment' => $comment];
    }

    private function createReply(Comment $comment, Comment $parent, string $content, string $type)
    {
        $comment->setParent($parent)
            ->setContent($content);

        $this->commentRepository->save($comment);

        return ['success' => true, 'parent' => $parent, 'subject' => $type == Comment::COMMENT_SUBJECT ? $parent->getSubjects()->getSlug(): $parent->getEvent()->getSlug(), 'comment' => $comment];
    }

    public function manage(Comment $comment, ?string $state, Request $request): void
    {
        if (!$comment) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }
        if (null == $state) {
            throw new Exception('erreur de validation');
        }

        match ($state) {
            'reject' => $this->commentRepository->remove($comment),
            'publish' => $this->commentRepository->save($comment->setIsActive(true))
        };
    }


}
