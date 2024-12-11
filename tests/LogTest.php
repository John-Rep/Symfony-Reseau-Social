<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class LogTest extends WebTestCase
{
    public function testDashboard(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/post');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
        $this->assertSelectorTextContains('div', 'not logged in');
    }

    public function testLogin(): void
    {
        $client = static::createClient();

        $users = static::getContainer()->get(UserRepository::class);

        $testuser = $users->findOneByEmail('jeremy@icloud.com');
        $client->loginUser($testuser);
        
        $crawler = $client->request('GET', '/post');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', 'Jeremy');
    }
}