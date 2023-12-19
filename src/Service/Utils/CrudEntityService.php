<?php

namespace App\Service\Utils;

use App\Service\Emails\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class CrudEntityService extends EntityService
{
    public function __construct(private SendMailService $mailService, private SluggerInterface $slugger, private EntityManagerInterface $em, private UserPasswordHasherInterface $encoder, private FormFactoryInterface $formFactory)
    {
        parent::__construct($mailService, $slugger, $em, $encoder);
    }

    /**
     * this function is used for any create or update action in this project.
     */
    public function createOrUpdate(object $entity, FormTypeInterface|string $formType, Request $request, bool $sendMail = false, array $property = null, object $additionnalEntity = null): mixed
    {
        $form = $this->formFactory->create($formType, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($sendMail) {
                $this->sendUserMail($entity);
            }

            $this->manageProperties($entity, $property);

            if (null !== $additionnalEntity) {
                $this->em->persist($additionnalEntity);
            }
            $this->em->persist($entity);
            $this->em->flush();

            return true;
        }

        return [
            'form' => $form->createView(),
            'entity' => $entity,
            'mode' => $entity->getId() ? 'edit' : 'create',
             null !== $additionnalEntity ? \mb_strtolower($this->getEntityName($additionnalEntity)) : null => $additionnalEntity,
        ];
    }

    public function delete(object $entity = null): void
    {
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('%s not found', get_class($entity)));
        }

        $this->em->remove($entity);
        $this->em->flush();
    }
}
