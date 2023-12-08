<?php

namespace App\Service;

use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use PHPUnit\Util\Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentService
{

    public function __construct(private CommentRepository $commentRepository, private FormFactoryInterface $formFactory){}


    public function create(Subject $subject = null, User $user = null,Comment $parent=null,Request $request, ?string $content)
    {
        if(null ==$subject || null == $user)
        {
            throw new NotFoundHttpException('erreur de soumission');
        }

        $comment = (new Comment())
            ->setUser($user)
            ->setSubjects($subject);

   if(null !== $parent)
   {
       $comment->setParent($parent)
           ->setContent($content);

       $this->commentRepository->save($comment);

       return true;
   }
        $form = $this->formFactory->create(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->commentRepository->save($comment);

            return true;
        }

        return ['form' => $form->createView(), 'comment' => $comment];
    }

    public function manage(Comment $comment, ?string $state, Request $request): void
    {
        if(!$comment)
        {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }
        if(null == $state)
        {
            throw  new Exception('erreur de validation');
        }

        match ($state){
            'reject'=>$this->commentRepository->remove($comment),
            'publish' =>$this->validate($comment)
        };
    }


    private function validate(Comment $comment): void
    {
        $this->commentRepository->save($comment->setIsActive(true));
    }
}