<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Form\CreateSubjectType;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/sujets', name: 'subjects_')]
class SubjectController extends AbstractController
{
    public function __construct(private SubjectRepository $subjectRepository, private SluggerInterface $slugger)
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $subjects = $this->subjectRepository->findAll();

        return $this->render('subject/index.html.twig', [
            'subjects' => $subjects,
        ]);
    }

    #[Route('/{slug}/show', name: 'show', methods: ['get'])]
    public function show(Subject $subject = null): Response
    {
        if (!$subject) {
            return $this->render('errors/404.html.twig');
        }

        return $this->render('subject/show.html.twig', \compact('subject'));
    }

    #[Route('/creation', name: 'create')]
    public function add(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('not allowed');
        }
        $subject = (new Subject())
            ->setOwnerId($this->getUser())
            ->setStatus('draft')
        ;

        $form = $this->createForm(CreateSubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setSlug($this->slugger->slug($subject->getTitle()));
            $this->subjectRepository->save($subject);

            return $this->redirectToRoute('subjects_index');
        }

        return $this->render('subject/add.html.twig',
            [
                'form' => $form->createView(),
                'user' => $subject,
            ]);
    }

    #[Route('/{slug}/edit', name: 'edit')]
    public function edit(Subject $subject = null, Request $request)
    {
        if (!$subject) {
            return $this->render('errors/404.html.twig');
        }

        $form = $this->createForm(CreateSubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->subjectRepository->save($subject);

            // on redirige vers la page des sujets
            return $this->redirectToRoute('subjects_index');
        }

        return $this->render('subject/add.html.twig', [
            'user' => $subject,
            'form' => $form->createView(),
        ]);
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
}
