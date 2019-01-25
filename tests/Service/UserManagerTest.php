<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Exception\User\UnknownUserException;
use App\Lib\Helper;
use App\Service\MailerService;
use App\Service\UserManager;
use App\Tests\AppWebTestCase;
use Faker\Factory;

class UserManagerTest extends AppWebTestCase
{
    /**
     * @var UserManager
     */
    protected $manager;

    protected function setUp()
    {
        parent::setUp();

        $mailer = $this->createMock(MailerService::class);
        $mailer->method('send')->willReturn(true);

        $this->manager = new UserManager(self::$container, $mailer);
    }

    /**
     * @throws \Exception
     */
    public function testPureUserRegisterException()
    {
        $this->expectException(UnknownUserException::class);

        $this->manager->registerUser(new User());
    }

    /**
     * @throws \Exception
     */
    public function testPureUserResetPasswordException()
    {
        $this->expectException(UnknownUserException::class);

        $this->manager->resetUserPassword(new User());
    }

    /**
     * @throws \Exception
     */
    public function testUserRegister()
    {
        self::$purger->purge();
        $faker = Factory::create();
        $email = $faker->email;

        $user = new User();
        $user->setUsername($faker->unique()->firstName);
        $user->setEmail($email);
        $user->setFirstname($faker->firstName);
        $user->setPlainPassword('123');
        $user->setActivateHash(Helper::generateToken());
        $user->setApiKey(Helper::generateToken());

        $this->manager->registerUser($user);
        $sameUser = self::$em->getRepository(User::class)
            ->findOneBy(['email' => $user->getEmail()]);

        $this->assertTrue($user === $sameUser);
        $this->assertSame($sameUser, $user);
        $this->assertEquals($email, $user->getEmail());
        $this->assertFalse($user->isActivated());
        $this->assertFalse($user->isDisabled());
        $this->assertEquals(0, $user->getRank());
        $this->assertNotEmpty($user->getApiKey());
        $this->assertNotEmpty($user->getActivateHash());

        return $user;
    }

    /**
     * @depends testUserRegister
     * @throws \Exception
     */
    public function testUserActivate(User $user)
    {
        $fakeHash = md5(microtime(true));
        $realHash = $user->getActivateHash();

        $this->assertFalse($this->manager->activateUser($fakeHash));
        $this->assertTrue($this->manager->activateUser($realHash));
        $this->assertFalse($this->manager->activateUser($realHash));

        return $user;
    }

    /**
     * @depends testUserActivate
     * @throws \Exception
     */
    public function testUserResetPassword(User $user)
    {
        $user = self::$em->getRepository(User::class)->find($user->getId());

        $oldPassword = $user->getPassword();
        $this->manager->resetUserPassword($user);

        $this->assertNotEquals($oldPassword, $user->getPassword());
    }
}