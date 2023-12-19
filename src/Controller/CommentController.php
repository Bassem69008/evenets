<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Subject;
use App\Repository\CommentRepository;
use App\Service\CommentService;
use App\Service\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commentaires', name: 'comments_')]
class CommentController extends AbstractController
{
    public function __construct(private CommentService $commentService, private CommentRepository $commentRepository, private PaginatorService $paginatorService)
    {
    }

    #[IsGranted('ROLE_BOARD')]
    #[Route('', name: 'index')]
    public function index(Request $request): Response
    {
        if (!$this->isGranted('ROLE_BOARD')) {
            throw $this->createAccessDeniedException('not allowed');
        }
        $comments = $this->commentRepository->findAll();

        return $this->render('comment/comments.html.twig', [
            'comments' => $this->paginatorService->paginate($comments, $request),
        ]);
    }

    #[Route('/{slug}/{parent}/creation', name: 'create', methods: ['get', 'post'])]
    public function create(Subject $subject = null, Comment $parent = null, Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('not allowed');
        }

        $content = $request->get('_comment');
        $result = $this->commentService->create($subject, $this->getUser(), $parent, $request, $content);
        if (true === $result['success']) {
            return $this->redirectToRoute('subjects_show', ['slug' => $subject->getSlug()]);
        }

        return $this->render('comment/index.html.twig', $result);
    }

    #[IsGranted('ROLE_BOARD')]
    #[Route('/{id}/{state}/manage', name: 'manage')]
    public function validateComment(Comment $comment, ?string $state, Request $request)
    {
        $this->commentService->manage($comment, $state, $request);

        return $this->render('comment/comments.html.twig', [
            'comments' => $this->paginatorService->paginate($this->commentRepository->findAll(), $request),
        ]);
    }
}
