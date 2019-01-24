<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Lib\Like\LikeableInterface;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testBasicStates()
    {
        $comment = new Comment();

        $this->assertNull($comment->getOwner());
        $this->assertNull($comment->getArticle());
        $this->assertNull($comment->getParent());
        $this->assertEmpty($comment->getChildren());
        $this->assertCount(0, $comment->getChildren());
        $this->assertFalse($comment->getBlocked());
        $this->assertInstanceOf(\DateTime::class, $comment->getCreatedAt());
        $this->assertInstanceOf(LikeableInterface::class, $comment);
    }

    public function testParentChildren()
    {
        $comment = new Comment();
        $child = new Comment();

        $this->assertSame($comment, $comment->addChild($child));
        $this->assertNotEmpty($comment->getChildren());
        $this->assertTrue($comment->getChildren()->contains($child));
        $this->assertCount(1, $comment->getChildren());

        $this->assertSame($comment, $comment->removeChild($child));
        $this->assertEmpty($comment->getChildren());
        $this->assertFalse($comment->getChildren()->contains($child));
        $this->assertCount(0, $comment->getChildren());
    }
}