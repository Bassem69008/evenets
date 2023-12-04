<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerUserTest extends WebTestCase
{
    public function testGetUsers()
    {
        $client = static::createClient();
        $client->request('GET', 'admin/users/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
    }

    public function testUserDetailNotFound()
    {
        $client = static::createClient();
        $client->request('GET', 'admin/users/431/show/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
