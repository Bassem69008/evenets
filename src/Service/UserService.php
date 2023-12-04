<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private FormFactoryInterface $formFactory, private EntityManagerInterface $em,
        private SendMailService $mail, private UserPasswordHasherInterface $encoder,
        private UserRepository $userRepository
    ) {
    }

    public function create(Request $request, bool $isSuccess)
    {
    }

    public function edit()
    {
    }

    public function processFile($uploadedFile): void
    {
        $spreadsheet = IOFactory::load($uploadedFile);
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $this->saveUsers($data);
    }

    public function saveUsers(array $data): void
    {
        // on supprime tous les utilisateurs pour eviter la duplication
        $this->removeUsers();

        array_shift($data);
        foreach ($data as $row) {
            $user = (new User())
                ->setLastname($row[0] ?? '')
                ->setFirstname($row[1] ?? '')
                ->setEmail($row[2] ?? '')
                ->setDo($row[3] ?? '')
                ->setSite($row[4] ?? '')
                ->setPassword($this->createPassword())

            ;
            $this->em->persist($user);
            //   // envoi de mail pour l'utilisateur
            $this->sendMail($user);
        }
        $this->em->flush();
    }

    public function sendMail(User $user): void
    {
        $this->mail->send(
            'no-reply@monsite.net',
            $user->getEmail(),
            'Ajout de votre compte',
            'addUser',
            [
                'user' => $user,
                'password' => $user->getPassword(),
            ]
        );
    }

    public function createPassword($length = 8)
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
