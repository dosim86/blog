<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppWebTestCase extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @var EntityManagerInterface
     */
    protected static $em;

    /**
     * @var ORMPurger
     */
    protected static $purger;

    protected static function init()
    {
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();
        self::$em = self::$container->get('doctrine')->getManager();
        self::$purger = new ORMPurger(self::$em);
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
}