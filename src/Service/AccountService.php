<?php

namespace App\Service;

use App\Entity\User;
use App\Form\UserCreateType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;


class AccountService
{
    public function __construct(private FormFactoryInterface $formFactory, private UserRepository $userRepository){}

    public function editProfile(User $user = null,Request $request)
    {
        if (!$user) {
            throw new NotFoundHttpException('Utilisateur Introuvable');
        }

        $form = $this->formFactory->create(UserCreateType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->userRepository->save($user);

            return true;
        }

        return ['form' => $form->createView(), 'user' => $user];
    }

}