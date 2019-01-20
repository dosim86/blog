<?php

namespace App\Subscriber;

use App\Entity\Article;
use App\Event\ArticleEvent;
use App\Service\QueueService;
use App\Worker\ArticleWorker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ArticleSubscriber implements EventSubscriberInterface
{
    private $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public static function getSubscribedEvents()
    {
        return [
            ArticleEvent::WATCH => 'onArticleWatch',
            KernelEvents::RESPONSE => 'onKernelReponse',
        ];
    }

    /**
     * @throws \Exception
     */
    public function onArticleWatch(ArticleEvent $event)
    {
        $article = $event->getArticle();
        $cookie = $event->getRequest()->cookies;
        $cookieName = md5('article_'.$article->getSlug());

        if ($article && !$cookie->has($cookieName)) {
            $attributes = $event->getRequest()->attributes;
            $attributes->set(ArticleEvent::WATCH, $cookieName);

            $this->queueService->addTask(ArticleWorker::WATCHED, [
                'articleId' => $article->getId()
            ]);
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