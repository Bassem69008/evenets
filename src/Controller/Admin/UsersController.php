<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UploadFileType;
use App\Repository\UserRepository;
use App\Service\PaginatorService;
use App\Service\Emails\SendMailService;
use App\Service\Users\UploadUsersService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users', name: 'users_')]
#[IsGranted('ROLE_ADMIN')]
class UsersController extends AbstractController
{
    public function __construct(
        private UserService $userService, private UserRepository $userRepository,
        private UserPasswordHasherInterface $encoder, private SendMailService $mail,
        private PaginatorService $paginator,
        private UploadUsersService $uploadUsersService
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {

       if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->render('errors/403.html.twig');
        }

        return $this->render('admin/users/index.html.twig', [
            'pagination' => $this->paginator->paginate($this->userRepository->findAll(), $request),
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['get'])]
    public function show(User $user = null): Response
    {
        if (!$user) {
            return $this->render('errors/404.html.twig');
        }

        return $this->render('admin/users/show.html.twig', \compact('user'));
    }

    #[Route('/creation', name: 'create', methods: ['GET','POST'])]
    public function add(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('not allowed');
        }
        try{

            $result = $this->userService->create($request);

            if (true === $result) {
                return $this->redirectToRoute('users_index');
            }
            return $this->render('admin/users/add.html.twig', $result);
        }catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(User $user = null, Request $request)
    {
        try {
            $result = $this->userService->edit($user, $request);

            if (true === $result) {
                return $this->redirectToRoute('users_index');
            }

            return $this->render('admin/users/add.html.twig', $result);
        }catch(NotFoundHttpException $e)
        {
            return $this->render('errors/404.html.twig');
        }
    }


    #[Route('/{id}/delete', name: 'delete', methods: ['get'])]
    public function delete(User $user = null, Request $request)
    {
        try{
            $this->userService->delete($user, $request);
            return $this->redirectToRoute('users_index');
        }catch(NotFoundHttpException $e)
        {
            return $this->render('errors/404.html.twig');
        }
    }

    #[Route('/upload', name: 'upload')]
    public function upload(Request $request)
    {
        try{
           $result= $this->uploadUsersService->uploadUsers($request);
           if(true === $result)
           {
               return $this->redirectToRoute('users_index');
           }
            return $this->render('upload/upload.html.twig', $result);
        }catch(\Exception $e)
        {
            return $e->getMessage();
        }

    }
}
