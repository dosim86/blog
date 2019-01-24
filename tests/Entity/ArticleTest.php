<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\BookmarkArticle;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testCommentBasic()
    {
        $article = new Article();

        $this->assertEmpty($article->getComments());
        $this->assertCount(0, $article->getComments());
        $this->assertEquals(0, $article->getCommentCount());

        return $article;
    }

    /**
     * @depends testCommentBasic
     */
    public function testAddComment(Article $article)
    {
        $comment = $this->createMock(Comment::class);
        $comment
            ->method('setArticle')
            ->withConsecutive([$article], [null])
            ->will($this->onConsecutiveCalls($comment, $comment));
        $comment
            ->method('getArticle')
            ->will($this->onConsecutiveCalls($article, $article, null));
        $article->addComment($comment);

        $this->assertSame($article, $comment->getArticle());
        $this->assertNotEmpty($article->getComments());
        $this->assertCount(1, $article->getComments());
        $this->assertEquals(1, $article->getCommentCount());

        return $article;
    }

    /**
     * @depends testAddComment
     */
    public function testRemoveComment(Article $article)
    {
        /** @var Comment $comment */
        $comment = $article->getComments()->first();
        $article->removeComment($comment);

        $this->assertNull($comment->getArticle());
        $this->assertEmpty($article->getComments());
        $this->assertCount(0, $article->getComments());
        $this->assertEquals(0, $article->getCommentCount());
    }

    public function testBookmarkBasic()
    {
        $article = new Article();

        $this->assertEmpty($article->getBookmarkArticles());
        $this->assertCount(0, $article->getBookmarkArticles());
        $this->assertEquals(0, $article->getBookmarkCount());

        return $article;
    }

    /**
     * @depends testBookmarkBasic
     */
    public function testAddBookmark(Article $article)
    {
        $bookmark = $this->createMock(BookmarkArticle::class);
        $bookmark
            ->method('setArticle')
            ->withConsecutive([$article], [null])
            ->will($this->onConsecutiveCalls($bookmark, $bookmark));
        $bookmark
            ->method('getArticle')
            ->will($this->onConsecutiveCalls($article, $article, null));

        $article = $article->addBookmarkArticle($bookmark);

        $this->assertSame($article, $bookmark->getArticle());
        $this->assertNotEmpty($article->getBookmarkArticles());
        $this->assertCount(1, $article->getBookmarkArticles());
        $this->assertEquals(1, $article->getBookmarkCount());

        return $article;
    }

    /**
     * @depends testAddBookmark
     */
    public function testRemoveBookmark(Article $article)
    {
        /** @var BookmarkArticle $bookmark */
        $bookmark = $article->getBookmarkArticles()->first();
        $article->removeBookmarkArticle($bookmark);

        $this->assertNull($bookmark->getArticle());
        $this->assertEmpty($article->getBookmarkArticles());
        $this->assertCount(0, $article->getBookmarkArticles());
        $this->assertEquals(0, $article->getBookmarkCount());
    }
}