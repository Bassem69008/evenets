<?php

namespace App\Service\Utils;

use App\Service\Emails\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function get_class;
use function mb_strtolower;
use function strtoupper;

class EntityService
{
    public function __construct(
        private SendMailService $mailService,
        private SluggerInterface $slugger,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $encoder
    ){}
    public  function sendUserMail(object $entity): void
    {
        $this->mailService->send(
            'no-reply@monsite.net',
            $entity->getEmail(),
            'Ajout de votre compte',
            'addUser',
            [
                'user' => $entity,
                'password' => $entity->getPassword(),
            ]
        );
    }

    /**
     * manage fields that require extra processing
     */
    public function manageProperties(object $entity, ?array $properties): void
    {
        if ($properties !== null) {
            foreach ($properties as $property) {
                switch ($property) {
                    case "slug":
                        $entity->setSlug($this->slugger->slug($entity->getTitle()));
                        break;
                    case "password":
                        $entity->setPassword($this->encoder->hashPassword($entity, $entity->getPassword()));
                        break;
                }
            }
        }
    }

    public   function getEntityName(?object $entity = null): ?string
    {

        $metadata = $this->em->getClassMetadata(get_class($entity));
        $reflectionClass = $metadata->getReflectionClass();

        // Check if ReflectionClass is available (Doctrine ORM 2.9+)
        if ($reflectionClass) {
            return $reflectionClass->getShortName();
        }

        // Fallback for older versions of Doctrine ORM
        return $metadata->name;


    }
}