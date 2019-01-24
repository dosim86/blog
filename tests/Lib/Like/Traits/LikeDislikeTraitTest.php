<?php

namespace App\Tests\Lib\Like\Traits;

use App\Lib\Like\Traits\LikeDislikeTrait;
use PHPUnit\Framework\TestCase;

class LikeDislikeTraitTest extends TestCase
{
    public function testLikeMethods()
    {
        $mock = $this->getMockForTrait(LikeDislikeTrait::class);
        $this->assertEquals(0, $mock->getLikeCount());

        $mock->incLikeCount();
        $this->assertEquals(1, $mock->getLikeCount());

        $mock->incLikeCount();
        $mock->incLikeCount();
        $this->assertEquals(3, $mock->getLikeCount());

        $mock->decLikeCount();
        $mock->decLikeCount();
        $this->assertEquals(1, $mock->getLikeCount());

        $mock->decLikeCount();
        $this->assertEquals(0, $mock->getLikeCount());

        $mock->decLikeCount();
        $this->assertEquals(0, $mock->getLikeCount());

        $mock->decLikeCount();
        $mock->decLikeCount();
        $this->assertEquals(0, $mock->getLikeCount());
    }

    public function testDislikeMethods()
    {
        $mock = $this->getMockForTrait(LikeDislikeTrait::class);
        $this->assertEquals(0, $mock->getDislikeCount());

        $mock->incDislikeCount();
        $this->assertEquals(1, $mock->getDislikeCount());

        $mock->incDislikeCount();
        $mock->incDislikeCount();
        $this->assertEquals(3, $mock->getDislikeCount());

        $mock->decDislikeCount();
        $mock->decDislikeCount();
        $this->assertEquals(1, $mock->getDislikeCount());

        $mock->decDislikeCount();
        $this->assertEquals(0, $mock->getDislikeCount());

        $mock->decDislikeCount();
        $this->assertEquals(0, $mock->getDislikeCount());

        $mock->decDislikeCount();
        $mock->decDislikeCount();
        $this->assertEquals(0, $mock->getDislikeCount());
    }
}