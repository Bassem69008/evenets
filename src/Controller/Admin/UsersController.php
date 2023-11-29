<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserCreateType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use function compact;
use function dd;
use function json_decode;

#[Route('/admin/users', name:'users_', methods: ['get'])]
class UsersController extends AbstractController
{

    public function __construct(private UserService $userService, private UserRepository $userRepository, private UserPasswordHasherInterface $encoder, private SendMailService $mail)
    {

    }
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users
        ]);
    }


    #[Route('/creation', name: 'create', methods: ['post'] )]
    public function add(Request $request)

    {
        $user = new User();
        // on crée le mot de passe etv on le set
        $password = $this->userService->createPassword();
        $user->setPassword($this->encoder->hashPassword($user,$password));

        // on crée le formulaire
        $form = $this->createForm(UserCreateType::class,$user);
        $form->handleRequest($request);

        // on traite le formulaire
        if($form->isSubmitted() && $form->isValid())
        {

            $this->userRepository->save($user);
            // envoi de mail pour l'utilisateur
            $this->mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Ajout de votre compte',
                'addUser',
                [
                    'user' =>$user,
                    'password'=> $password
                ]
            );

            // on redirige  vers la page des users
            return $this->redirectToRoute('users_index');
        }

        return $this->render('admin/users/add.html.twig',
        [
            'form'=>$form->createView(),
            'user'=>$user
        ]);
    }

   #[Route('/{id}/show', name:'show', methods: ['get'])]
    public function show(User $user =null): Response
    {
       if(!$user)
       {
           return $this->render('errors/404.html.twig');
       }
       dd($user);
       return $this->render('admin/users/show.html.twig',compact('user'));
    }

    #[Route('/{id}/edit', name:'edit')]
    public function edit(User $user=null, Request $request)
    {
        if(!$user)
        {
            return $this->render('errors/404.html.twig');
        }

        $form = $this->createForm(UserCreateType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {


            // on enregistre les données

            $this->userRepository->save($user);
            // on redirige vers la page des utilisateurs
            return $this->redirectToRoute('users_index');
        }

        return $this->render('admin/users/edit.html.twig',[
            'user' =>$user,
            'form' =>$form->createView()
        ]);
    }
   #[Route('/{id}/delete', name:'delete', methods: ['get'])]
    public function delete(User $user=null, Request $request)
    {
        if(!$user)
        {
            return $this->render('errors/404.html.twig');
        }
        $this->userRepository->remove($user);
        return $this->redirectToRoute('users_index');
    }

    #[Route('/upload', name:'upload')]
    public function upload()
    {

    }
}
