<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Entity\SubjectLike;
use App\Repository\SubjectLikeRepository;
use App\Repository\SubjectRepository;
use App\Service\PaginatorService;
use App\Service\SubjectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/sujets', name: 'subjects_')]
class SubjectController extends AbstractController
{
    public function __construct(
        private SubjectRepository $subjectRepository, private SluggerInterface $slugger,
        private PaginatorService $paginator,
        private Security $security, private WorkflowInterface $subjectPublishing,
        private SubjectService $subjectService,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $subjects = $this->subjectRepository->findAll();

        return $this->render('subject/index.html.twig', [
            'pagination' => $this->paginator->paginate($subjects, $request),
        ]);
    }

    #[Route('/{slug}/show', name: 'show', methods: ['get'])]
    public function show(Subject $subject = null): Response
    {
        try {
            $subject = $this->subjectService->show($subject);

            return $this->render('subject/show.html.twig', \compact('subject'));
        } catch (NotFoundHttpException $e) {
            return $this->render('errors/404.html.twig');
        }
    }

    #[Route('/{slug}/show/request-review', name : 'request_review')]
    public function requestReview(Subject $subject, Request $request)
    {
        $subject->setStatus(Subject::STATUS_REVIEWED);
        $this->subjectPublishing->can($subject, 'to_review');

        $this->subjectRepository->save($subject);

        return $this->render('subject/show.html.twig', \compact('subject'));
    }

    #[Route('/{slug}/{state}/show/review', name: 'review')]
    public function review(Subject $subject, string $state = null, Request $request)
    {
        try {
            $subject = $this->subjectService->review($subject, $state);

            return $this->render('subject/show.html.twig', \compact('subject'));
        } catch (NotFoundHttpException $e) {
            return $this->render('errors/404.html.twig');
        }
    }

    #[Route('/creation', name: 'create')]
    public function add(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('not allowed');
        }

        $result = $this->subjectService->create($request, $this->getUser());
        if (true === $result) {
            return $this->redirectToRoute('subjects_index');
        }

        return $this->render('subject/add.html.twig', $result);
    }

    #[Route('/{slug}/edit', name: 'edit')]
    public function edit(Subject $subject = null, Request $request)
    {
        try {
            $result = $this->subjectService->edit($subject, $request);

            if (true === $result) {
                return $this->redirectToRoute('subjects_index');
            }

            return $this->render('subject/add.html.twig', $result);
        } catch (NotFoundHttpException $e) {
            return $this->render('errors/404.html.twig');
        }
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['get'])]
    public function delete(Subject $subject = null, Request $request)
    {
        if (!$subject) {
            return $this->render('errors/404.html.twig');
        }
        $this->subjectRepository->remove($subject);

        return $this->redirectToRoute('subjects_index');
    }

    /**
     * @return Response
     *                  Liker ou Unliker un sujet
     */
    #[Route('/{id}/like', name: 'like', methods: ['get'])]
    public function like(Subject $subject, EntityManagerInterface $manager, SubjectLikeRepository $likeRepo): Response
    {
        // l'utilisateur n'est pas connecté
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN') || !$user) {
            return $this->json([
                'code' => 403,
                'message' => 'unauthorized',
            ], 403);
        }

        // le user est connecté est connecté et aime le sujet =>supprimer le like
        if ($subject->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy(['subject' => $subject, 'user' => $user]);
            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'like supprimé',
                'likes' => $likeRepo->count(['subject' => $subject]),
            ], 200);
        }
        $like = new SubjectLike();
        $like->setSubject($subject);
        $like->setUser($user);

        $manager->persist($like);
        $manager->flush();

        // le user est connecté n'aime âs le sujet => ajouter un like
        return $this->json([
            'code' => 200,
            'message' => 'like ajouté',
            'likes' => $likeRepo->count(['subject' => $subject]),
        ], 200);
    }
}
