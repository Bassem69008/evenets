<?php

namespace App\Service;

use App\Entity\User;
use App\Form\UserCreateType;
use App\Repository\UserRepository;
use App\Service\Emails\SendMailService;
use App\Service\SpreadSheet\SpreadSheetService;
use App\Service\Utils\CrudEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public const PASSWORD_PROPERTY = 'password';

    public function __construct(
        private FormFactoryInterface $formFactory, private EntityManagerInterface $em,
        private SendMailService $mail, private UserPasswordHasherInterface $encoder,
        private UserRepository $userRepository, private CrudEntityService $entityService,
        private SendMailService $mailService,
        private SpreadSheetService $sheetService
    ) {
    }

    public function create(Request $request): mixed
    {
        $user = new User();
        $user->setPassword($this->createPassword());
        $properties = [self::PASSWORD_PROPERTY];

        return $this->entityService->createOrUpdate($user, UserCreateType::class, $request, true, $properties);
    }

    public function edit(User $user = null, Request $request): mixed
    {
        return $this->entityService->createOrUpdate($user, UserCreateType::class, $request, false, null);
    }

    public function delete(User $user = null, Request $request): Response
    {
        $this->entityService->delete($user, $request);
    }

    /**
     * create random password.
     */
    public function createPassword($length = 8): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; ++$i) {
            $string .= $chars[rand(0, strlen($chars) - 1)];
        }

        return $string;
    }

    public function removeUsers(): void
    {
        $users = $this->userRepository->getUserByRoles();

        foreach ($users as $user) {
            $this->userRepository->remove($user);
        }
    }
}
