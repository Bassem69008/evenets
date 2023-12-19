<?php

namespace App\Tests\Controller;

// use Symfony\Component\Panther\PantherTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class UsersControllerTest extends WebTestCase
{
    public const EMAIL_ADMIN = 'developer@exemple.com';
    public const EMAIL_BOARD = 'board@exemple.com';

    public function testIndexActionAsAdmin()
    {
        $client = static::createClient();

        // get an admin user
        $adminUser = $this->getUser(self::EMAIL_ADMIN);

        // Log in as the admin user
        $client->loginUser($adminUser);

        $crawler = $client->request('GET', '/admin/users');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
        $table = $crawler->filter('.table-hover');

        // Récupérer le contenu HTML de la réponse
        $htmlContent = $client->getResponse()->getContent();

        $adminEmail = self::EMAIL_ADMIN;
        $tdElementAdmin = $crawler->filter('table tr td:contains("'.self::EMAIL_ADMIN.'")');
        $tdElementBoard = $crawler->filter('table tr td:contains("'.self::EMAIL_BOARD.'")');

        // Assurer qu'il y a au moins un élément correspondant
        $this->assertGreaterThan(0, $tdElementAdmin->count(), 'Le td avec le contenu attendu n\'a pas été trouvé.');
        $this->assertGreaterThan(0, $tdElementBoard->count(), 'Le td avec le contenu attendu n\'a pas été trouvé.');
        $this->assertCount(5, $table->filter('tbody tr'));
    }

    public function testIndexActionAsNonAdmin()
    {
        $client = static::createClient();

        $nonAdminUser = $this->getUser(self::EMAIL_BOARD);

        // Log in as the non-admin user
        $client->loginUser($nonAdminUser);

        $client->request('GET', '/admin/users');
        // Check s'il ya une redirection
        if ($client->getResponse()->isRedirect()) {
            // Follow the redirect
            $client->followRedirect();
        }

        $this->assertSame(403, $client->getResponse()->getStatusCode());
    }

    public function testAddUser()
    {
        $client = static::createClient();
        // get an admin user
        $adminUser = $this->getUser(self::EMAIL_ADMIN);

        // Log in as the admin user
        $client->loginUser($adminUser);

        // Créez un utilisateur fictif pour utiliser dans le formulaire
        $userData = [
            'email' => 'email@email.com',
            'lastname' => 'toto lastname',
            'firstname' => 'toto firstname',
            'password' => 'password',
            // Ajoutez d'autres champs nécessaires
        ];

        // Faites une demande POST pour simuler la soumission du formulaire
        $crawler = $client->request(Request::METHOD_POST, '/admin/users/creation', ['form' => $userData]);
        dd($crawler);

        // Vérifiez que la réponse est une redirection vers la liste des utilisateurs
        //  $this->assertTrue($client->getResponse()->isRedirect('/users/'));

        // Récupérez l'utilisateur nouvellement créé depuis la base de données (vous devrez ajuster cela en fonction de votre logique)
        $createdUser = $this->getUserFromDatabase($userData['email']);
        dd($createdUser);

        // Vérifiez que l'utilisateur a été correctement créé
        //  $this->assertInstanceOf(User::class, $createdUser);
        $this->assertSame($userData['lastname'], $createdUser->getLastname());
        $this->assertSame($userData['firstname'], $createdUser->getFirstname());
        $this->assertSame($userData['email'], $createdUser->getEmail());

        // Ajoutez d'autres assertions au besoin
    }

    // Méthode pour récupérer l'utilisateur depuis la base de données
    private function getUserFromDatabase($email)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        return $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    private function getUser(string $email): User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getContainer()->get(UserRepository::class);

        return $userRepository->findOneByEmail($email);
    }
}
