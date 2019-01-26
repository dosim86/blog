<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppWebTestCase extends WebTestCase
{
    /**
     * @var EntityManager
     */
    protected static $em;

    /**
     * @var ORMPurger
     */
    protected static $purger;
    /**
     * @var Client
     */
    protected static $client;

    protected static function init()
    {
        self::bootKernel();
        self::$em = self::$container->get('doctrine')->getManager();
        self::$purger = new ORMPurger(self::$em);
        self::$client = self::createClient();
    }

    protected function setUp()
    {
        parent::setUp();

        self::init();
    }

    protected function tearDown()
    {
        parent::tearDown();

        self::$em->close();
        self::$em = null;
    }

    /**
     * @throws \Exception
     */
    protected function clearUser($email)
    {
        $userRepository = self::$em->getRepository(User::class);
        if ($testUser = $userRepository->findOneBy(['email' => $email])) {
            $testUser = self::$em->merge($testUser);
            self::$em->remove($testUser);
            self::$em->flush();
        }
    }

    /**
     * @throws \Exception
     */
    protected function getUserByEmail($email): User
    {
        return self::$em->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }
}