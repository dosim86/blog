<?php

namespace App\Subscriber;

use App\Entity\Article;
use App\Event\ArticleEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ArticleSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            ArticleEvent::WATCH => 'onArticleWatch',
            KernelEvents::RESPONSE => 'onKernelReponse',
        ];
    }

    public function onArticleWatch(ArticleEvent $event)
    {
        $article = $event->getArticle();
        $cookie = $event->getRequest()->cookies;

        if ($article && !$cookie->has($cookieName = md5('article_'.$article->getSlug()))) {
            $article->incWatchCount();
            $this->em->persist($article);
            $this->em->flush();

            $attributes = $event->getRequest()->attributes;
            $attributes->set(ArticleEvent::WATCH, $cookieName);
        }
    }

    public function onKernelReponse(FilterResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        if ($attributes->has(ArticleEvent::WATCH)) {
            $cookieName = $attributes->get(ArticleEvent::WATCH);
            $cookieExpires = $_SERVER['REQUEST_TIME'] + Article::WATCH_EXPIRES;

            $event->getResponse()->headers->setCookie(new Cookie($cookieName, md5(time()), $cookieExpires));
            $attributes->remove(ArticleEvent::WATCH);
        }
    }
}