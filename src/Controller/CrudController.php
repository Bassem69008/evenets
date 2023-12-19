<?php

namespace App\Controller;

use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CrudController extends AbstractController
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_BOARD = 'ROLE_BOARD';
    public const ACCESS_DENIED_PAGE = 'errors/403.html.twig';
    public const NOT_FOUND_PAGE = 'errors/403.html.twig';

    public function __construct(
        private PaginatorService $paginatorService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function indexWithPagination(Request $request, string $template, ServiceEntityRepositoryInterface|string $repository, ?bool $isGranted = false, string $role = null): Response
    {
        // check GrantedRole
        $this->checkGranted($isGranted, $role);

        // Fetch the repository based on the provided argument
        $repositoryInstance = $repository instanceof ServiceEntityRepositoryInterface
            ? $repository
            : $this->entityManager->getRepository($repository);

        return $this->render($template, [
            'pagination' => $this->paginatorService->paginate($repositoryInstance->findAll(), $request),
        ]);
    }

    public function showEntity(object $entity = null, string $template): Response
    {
        // check entity
        $this->checkEntity($entity);
        $entityName = $this->getEntityName($entity);

        return $this->render($template,
            [
                \mb_strtolower($entityName) => $entity,
            ]
        );
    }

    public function addEntity($service, string $role = null, string $redirectionRoute, string $templateRender, Request $request, bool $isGranted = false)
    {
        // check GrantedRole
        $this->checkGranted($isGranted, $role);
        try {
            $result = $service->create($request);

            if (true === $result) {
                return $this->redirectToRoute($redirectionRoute);
            }

            return $this->render($templateRender, $result);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function editEntity(object $entity, $service, string $redirectionRoute, string $templateRender, string $role = null, Request $request, bool $isGranted = false)
    {
        // check GrantedRole
        $this->checkGranted($isGranted, $role);

        if (!$entity) {
            return $this->render(self::NOT_FOUND_PAGE);
        }
        try {
            $result = $service->edit($entity, $request);

            if (true === $result) {
                return $this->redirectToRoute($redirectionRoute);
            }

            return $this->render($templateRender, $result);
        } catch (NotFoundHttpException $e) {
            return $this->render(self::NOT_FOUND_PAGE);
        }
    }

    public function deleteEntity(object $entity = null, $service, string $redirectTemplate, Request $request)
    {
        try {
            $service->delete($entity, $request);

            return $this->redirectToRoute($redirectTemplate);
        } catch (NotFoundHttpException $e) {
            return $this->render(self::NOT_FOUND_PAGE);
        }
    }

    private function checkEntity(object $entity)
    {
        if (!$entity) {
            return $this->render(self::NOT_FOUND_PAGE);
        }
    }

    private function checkGranted(bool $isGranted = false, string $role = null)
    {
        // check GrantedRole
        if ($isGranted && null !== $role) {
            if (!$this->isGranted($role)) {
                return $this->render(self::ACCESS_DENIED_PAGE);
            }
        }
    }

    private function getEntityName(object $entity = null): ?string
    {
        $metadata = $this->entityManager->getClassMetadata(\get_class($entity));
        $reflectionClass = $metadata->getReflectionClass();

        // Check if ReflectionClass is available
        if ($reflectionClass) {
            return $reflectionClass->getShortName();
        }

        // Fallback for older versions of Doctrine ORM
        return $metadata->name;
    }
}
