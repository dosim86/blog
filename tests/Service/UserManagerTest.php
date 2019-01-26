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
        $faker = Factory::create();
        $userRepository = self::$em->getRepository(User::class);

        $email = 'test@test.com';
        $username = 'test';

        $this->clearUser($email, $username);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setFirstname($faker->firstName);
        $user->setPlainPassword('123');
        $user->setActivateHash(Helper::generateToken());
        $user->setApiKey(Helper::generateToken());

        $this->manager->registerUser($user);
        $sameUser = $userRepository->findOneBy(['email' => $user->getEmail()]);

        $this->assertTrue($user->getId() === $sameUser->getId());
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
        $oldPassword = $user->getPassword();
        $this->manager->resetUserPassword($user);

        $this->clearUser($user->getEmail(), $user->getUsername());

        $this->assertNotEquals($oldPassword, $user->getPassword());
    }
}