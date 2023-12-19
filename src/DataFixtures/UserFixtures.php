<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        /** Create fix  Admin  */
        $admin = new User();
        $admin->setLastname('Rahali');
        $admin->setFirstname('Bassem');
        $admin->setEmail('developer@exemple.com');
        $admin->setDo('DSF');
        $admin->setSite('Lyon');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));

        $manager->persist($admin);

        /** Create fix Board  */
        $board = new User();
        $board->setLastname('Ronaldo');
        $board->setFirstname('Cristiano');
        $board->setEmail('board@exemple.com');
        $board->setDo('DSF');
        $board->setSite('Lyon');
        $board->setRoles(['ROLE_BOARD']);
        $board->setPassword($this->passwordEncoder->hashPassword($board, 'passwordBoard'));

        $manager->persist($board);

        /** Create fix User  */
        $user = new User();
        $user->setLastname('Messi');
        $user->setFirstname('Lionel');
        $user->setEmail('user@exemple.com');
        $user->setDo('DSF');
        $user->setSite('Lyon');
        $user->setRoles(['ROLE_CONNECTED']);
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'passwordUser'));

        $manager->persist($user);

        /* Create Boards */
        $this->createUser(5, ['ROLE_BOARD'], 'passwordBoard', $manager);

        /* Create Users */
        $this->createUser(20, ['ROLE_USER'], 'passwordUser', $manager);

        $manager->flush();
    }

    public function createUser(int $number, array $role, string $password, ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // fausses données en francais
        for ($i = 1; $i <= $number; ++$i) {
            $user = new User();
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setEmail($faker->email);
            $user->setDo('DSF');
            $user->setSite('Lyon');
            $user->setRoles($role);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, $password)
            );
            $manager->persist($user);
            $this->setReference('usr-'.$i, $user);
        }
    }
}
