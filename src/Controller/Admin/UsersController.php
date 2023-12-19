<?php

namespace App\Controller\Admin;

use App\Controller\CrudController;
use App\Entity\User;
use App\Service\PaginatorService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users', name: 'users_')]
#[IsGranted('ROLE_ADMIN')]
class UsersController extends CrudController
{
    public const INDEX_USERS_TEMPLATE = 'admin/users/index.html.twig';
    public const INDEX_USERS_REDIRECTION = 'users_index';
    public const ADD_UPDATE_TEMPLATE = 'admin/users/addEdit.html.twig';

    public function __construct(PaginatorService $paginatorService, EntityManagerInterface $entityManager, private readonly UserService $userService)
    {
        parent::__construct($paginatorService, $entityManager);
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->indexWithPagination($request, self::INDEX_USERS_TEMPLATE, User::class, true, CrudController::ROLE_ADMIN);
    }

    #[Route('/{id}/show', name: 'show', methods: ['get'])]
    public function show(User $user = null): Response
    {
        return $this->showEntity($user, 'admin/users/show.html.twig');
    }

    #[Route('/creation', name: 'create', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        return $this->addEntity($this->userService, CrudController::ROLE_ADMIN, self::INDEX_USERS_REDIRECTION, self::ADD_UPDATE_TEMPLATE, $request, true);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(User $user = null, Request $request): Response
    {
        return $this->editEntity($user, $this->userService, self::INDEX_USERS_REDIRECTION, self::ADD_UPDATE_TEMPLATE, CrudController::ROLE_ADMIN, $request, true);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['get'])]
    public function delete(User $user = null, Request $request): Response
    {
        return $this->deleteEntity($user, $this->userService, self::INDEX_USERS_REDIRECTION, $request);
    }

    #[Route('/upload', name: 'upload')]
    public function upload(Request $request): Response
    {
        try {
            $result = $this->uploadUsersService->uploadUsers($request);
            if (true === $result) {
                return $this->redirectToRoute(self::INDEX_USERS_REDIRECTION);
            }

            return $this->render('upload/upload.html.twig', $result);
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }
    }
}
