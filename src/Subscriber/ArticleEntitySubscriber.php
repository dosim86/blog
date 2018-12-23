<?php

namespace App\Subscriber;

use App\Entity\Article;
use App\Helper\Html;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Container\ContainerInterface;

class ArticleEntitySubscriber implements EventSubscriber
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
        ];
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $article = $args->getObject();
        if ($article instanceof Article) {
            $article->setContent(Html::sanitizeJs($article->getContent()));
        }
    }
}