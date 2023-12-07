<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Repository\SubjectLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/like', name: 'like_')]
class SubjectLikeController extends AbstractController
{
    /**
     * @return Response
     *                  Liker ou Unliker un sujet
     */
    public function like(Subject $subject, EntityManagerInterface $manager, SubjectLikeRepository $likeRepo): Response
    {
        return $this->json(['code' => 200, 'message' => 'ca marche bien'], 200);
    }
}
