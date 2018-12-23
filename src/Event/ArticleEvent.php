<?php

namespace App\Event;

use App\Entity\Article;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ArticleEvent extends Event
{
    const WATCH = 'app.article.watch';

    private $request;

    private $article;

    public function __construct(Request $request, Article $article)
    {
        $this->request = $request;
        $this->article = $article;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}