<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTest extends TestCase
{
    public function testBasicStates()
    {
        $user = new User();

        $this->assertEmpty($user->getArticles());
        $this->assertEmpty($user->getComments());
        $this->assertEmpty($user->getBookmarkArticles());
        $this->assertEmpty($user->getFollowers());
        $this->assertEmpty($user->getSubscribs());
        $this->assertFalse($user->isDisabled());
        $this->assertFalse($user->isActivated());
        $this->assertEquals(0, $user->getRank());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertEquals('default.png', $user->getAvatar());
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(\DateTime::class, $user->getLastActivityAt());
    }

    public function testLastActivity()
    {
        $user = new User();

        $lastDate = $user->getLastActivityAt();
        $user->refreshLastActivity();

        $this->assertGreaterThan($lastDate, $user->getLastActivityAt());
    }
}