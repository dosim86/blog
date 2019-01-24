<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\BookmarkArticle;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testComment()
    {
        $article = new Article();
        $comment = $this->createMock(Comment::class);

        $this->assertEmpty($article->getComments());
        $this->assertCount(0, $article->getComments());
        $this->assertEquals(0, $article->getCommentCount());

        $comment->expects($this->once())
            ->method('getArticle')
            ->willReturn($article);
        $this->assertSame($article, $article->addComment($comment));
        $this->assertSame($article, $comment->getArticle());
        $this->assertNotEmpty($article->getComments());
        $this->assertCount(1, $article->getComments());
        $this->assertEquals(1, $article->getCommentCount());

        $comment->expects($this->once())
            ->method('setArticle')
            ->with(null)
            ->method('getArticle')
            ->willReturn(null);
        $this->assertSame($article, $article->removeComment($comment));
        $this->assertNull($comment->getArticle());
        $this->assertEmpty($article->getComments());
        $this->assertCount(0, $article->getComments());
        $this->assertEquals(0, $article->getCommentCount());
    }

    public function testBookmark()
    {
        $article = new Article();
        $bookmark = $this->createMock(BookmarkArticle::class);

        $this->assertEmpty($article->getBookmarkArticles());
        $this->assertCount(0, $article->getBookmarkArticles());
        $this->assertEquals(0, $article->getBookmarkCount());

        $bookmark->expects($this->once())
            ->method('getArticle')
            ->willReturn($article);
        $this->assertSame($article, $article->addBookmarkArticle($bookmark));
        $this->assertSame($article, $bookmark->getArticle());
        $this->assertNotEmpty($article->getBookmarkArticles());
        $this->assertCount(1, $article->getBookmarkArticles());
        $this->assertEquals(1, $article->getBookmarkCount());

        $bookmark->expects($this->once())
            ->method('setArticle')
            ->with(null)
            ->method('getArticle')
            ->willReturn(null);
        $this->assertSame($article, $article->removeBookmarkArticle($bookmark));
        $this->assertNull($bookmark->getArticle());
        $this->assertEmpty($article->getBookmarkArticles());
        $this->assertCount(0, $article->getBookmarkArticles());
        $this->assertEquals(0, $article->getBookmarkCount());
    }
}