<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;

class SecurityControllerTest extends AppWebTestCase
{
    private $userData = [
        'email' => 'misstilda@yandex.ru',
        'username' => 'test2',
        'password' => '1',
    ];

    private $userFormData;

    protected function setUp()
    {
        parent::setUp();

        $this->userFormData = [
            'register[email]' => $this->userData['email'],
            'register[username]' => $this->userData['username'],
            'register[plainPassword][first]' => $this->userData['password'],
            'register[plainPassword][second]' => $this->userData['password'],
        ];
    }

    /**
     * @throws \Exception
     */
    public function testRegisterUser()
    {
        $this->clearUser($this->userData['email']);

        $client = self::$client;
        $crawler = $client->request('POST', '/register');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign Up')->form();
        $form->setValues($this->userFormData);
        $crawler = $client->submit($form);
        $errors = $crawler->filter('.invalid-feedback');

        $this->assertCount(0, $errors);

        sleep(1); // gearman needs some times
    }

    /**
     * @depends testRegisterUser
     * @throws \Exception
     */
    public function testFailSameUserRegister()
    {
        $client = self::$client;
        $crawler = $client->request('POST', '/register');

        $form = $crawler->selectButton('Sign Up')->form();
        $form->setValues($this->userFormData);
        $crawler = $client->submit($form);

        $errors = $crawler->filter('.invalid-feedback');

        $this->assertGreaterThan(0, $errors->count());
        $this->assertContains('This email is already used', $errors->html());
    }

    /**
     * @depends testFailActivating
     * @throws \Exception
     */
    public function testSuccessActivating()
    {
        $this->assertNotNull($user = $this->getUserByEmail($this->userData['email']));

        self::$client->request('GET', '/activate/' . $user->getActivateHash());

        $this->assertTrue($user->isActivated());
    }

    /**
     * @depends testSuccessActivating
     * @throws \Exception
     */
    public function testFailLogIn()
    {
        $client = self::$client;
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form->setValues([
            'username' => $this->userData['username'],
            'password' => md5(microtime(true)),
        ]);

        $crawler = $client->submit($form);
        if ($client->getResponse()->isRedirection()) {
            $crawler = $client->followRedirect();
        }

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Invalid credentials', $crawler->html());
    }

    /**
     * @depends testFailLogIn
     * @throws \Exception
     */
    public function testSuccessLogIn()
    {
        $client = self::$client;
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign in')->form();
        $form->setValues([
            'username' => $this->userData['username'],
            'password' => $this->userData['password'],
        ]);

        $crawler = $client->submit($form);
        if ($client->getResponse()->isRedirection()) {
            $crawler = $client->followRedirect();
        }

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotContains('Username could not be found', $crawler->html());
    }
}