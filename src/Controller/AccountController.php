<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Service\AccountService;
use App\Service\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class AccountController extends AbstractController
{

    public function __construct(private AccountService $accountService, private SubjectRepository $subjectRepository,
    private UserRepository $userRepository, private PaginatorService $paginator){}

    #[Route('/profil', name:'profile')]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function profil(): Response
    {

       return $this->render('account/profil.html.twig',[
           'user'=>$this->getUser()
       ]);
    }

    #[Route('/profil/{id}/edit', name:'profile_edit')]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function edit(User $user=null, Request $request)
    {

        try {
            $result = $this->accountService->editProfile($user, $request);

            if (true === $result) {
                return $this->redirectToRoute('profile');
            }

            return $this->render('account/edit.html.twig', $result);
        } catch (NotFoundHttpException $e) {
            return $this->render('errors/404.html.twig');
        }

    }

    #[Route('/profil/{id}/sujets', name:'profile_subjects')]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function subjects(User $user=null, Request $request)
    {
        if(!$user)
        {
            return $this->render('errors/404.html.twig');
        }
        return $this->render('account/subjects.html.twig',[
            'pagination' => $this->paginator->paginate($user->getSubjects(), $request),
        ]);


    }
}