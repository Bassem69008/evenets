<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;




class AccountController extends AbstractController
{
    #[Route('/profil', name:'profile')]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function profil()
    {

       return $this->render('account/profil.html.twig',[
           'user'=>$this->getUser()
       ]);
    }
}