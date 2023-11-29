<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerUserTest extends WebTestCase
{

    public function testGUsers()
    {
        $client = static::createClient();
        $users = $client->request('GET', '/admin/users/');
        $this->assertResponseStatusCodeSame(200);

        //return json_decode((string) $client->getResponse()->getContent(), true);
    }




}