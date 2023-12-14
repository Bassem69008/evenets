<?php

namespace App\Service\Users;

use App\Entity\User;
use App\Form\UploadFileType;
use App\Form\UserCreateType;
use App\Repository\UserRepository;
use App\Service\Emails\SendMailService;
use App\Service\SpreadSheet\SpreadSheetService;
use App\Service\UserService;
use App\Service\Utils\CrudEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function array_shift;

class UploadUsersService extends UserService
{
    public function __construct(private FormFactoryInterface $formFactory, private EntityManagerInterface $em, private SendMailService $mail, private UserPasswordHasherInterface $encoder, private UserRepository $userRepository, private CrudEntityService $entityService, private SendMailService $mailService, private SpreadSheetService $sheetService)
    {
        parent::__construct($formFactory, $em, $mail, $encoder, $userRepository, $entityService, $mailService, $sheetService);
    }


    public function uploadUsers(Request $request)
    {

        $form = $this->formFactory->create(UploadFileType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();
            if ($uploadedFile) {
                $this->processFile($uploadedFile, $request);
            }

            return true;
        }
        return ['form' => $form->createView()];
    }

    public function saveUsers(array $data, Request $request): void
    {
        // on supprime tous les utilisateurs pour eviter la duplication
        $this->removeUsers();
        array_shift($data);
        foreach ($data as $row) {
            $password = $this->createPassword();
            $user = (new User())
                ->setLastname($row[0] ?? '')
                ->setFirstname($row[1] ?? '')
                ->setEmail($row[2] ?? '')
                ->setDo($row[3] ?? '')
                ->setSite($row[4] ?? '')

            ;

            $this->sendMail($user, $password);
            $user->setPassword($this->encoder->hashPassword($user,$password));
            $this->userRepository->save($user);

        }

    }

    public function sendMail(User $user, string $password): void
    {

        $this->mail->send(
            'no-reply@monsite.net',
            $user->getEmail(),
            'Ajout de votre compte',
            'addUser',
            [
                'user' => $user,
                'password' => $password
            ]
        );
    }

    public function processFile($uploadedFile, Request $request): void
    {
        $data = $this->sheetService->processFile($uploadedFile);

        $this->saveUsers($data, $request);
    }
}