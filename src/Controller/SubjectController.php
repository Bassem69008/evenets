<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Entity\SubjectLike;
use App\Form\CreateSubjectType;
use App\Repository\SubjectLikeRepository;
use App\Repository\SubjectRepository;
use App\Service\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/sujets', name: 'subjects_')]
class SubjectController extends AbstractController
{
    public function __construct(private SubjectRepository $subjectRepository, private SluggerInterface $slugger, private PaginatorService $paginator)
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $subjects = $this->subjectRepository->findAll();

        return $this->render('subject/index.html.twig', [
            'pagination'=>$this->paginator->paginate($subjects,$request),

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

    /**
     * @param Subject $subject
     * @param EntityManagerInterface $manager
     * @param SubjectLikeRepository $likeRepo
     * @return Response
     * Liker ou Unliker un sujet
     */
    #[Route('/{id}/like', name:'like', methods: ['get'])]
    public function like(Subject $subject, EntityManagerInterface $manager, SubjectLikeRepository $likeRepo): Response
    {
        // l'utilisateur n'est pas connecté
       $user= $this->getUser();

     if ($this->isGranted('ROLE_ADMIN') || !$user) {
            return $this->json([
                'code'=>403 ,
                'message'=> 'unauthorized'
            ],403);
        }


        // le user est connecté est connecté et aime le sujet =>supprimer le like
        if($subject->isLikedByUser($user)){
            $like= $likeRepo->findOneBy(['subject'=>$subject, 'user'=>$user]);
            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code'=>200 ,
                'message'=> 'like supprimé',
                'likes'=>$likeRepo->count(['subject'=>$subject])
            ],200);
        }
        $like = new SubjectLike();
        $like->setSubject($subject);
        $like->setUser($user);

        $manager->persist($like);
        $manager->flush();

        // le user est connecté n'aime âs le sujet => ajouter un like
        return $this->json([
            'code'=>200 ,
            'message'=> 'like ajouté',
            'likes'=>$likeRepo->count(['subject'=>$subject])
        ],200);
    }
}
