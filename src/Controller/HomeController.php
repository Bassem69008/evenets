<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_BOARD'))
        {
            return $this->redirectToRoute('dashboard_index');
        }
        else
        {
            return $this->redirectToRoute('subjects_index');

        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
