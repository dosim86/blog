<?php

namespace App\Worker;

use App\Command\Worker\WorkerInterface;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ArticleWorker implements WorkerInterface
{
    const WATCHED = 'article.watched';

    private $em;

    private $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $appLogger)
    {
        $this->em = $em;
        $this->logger = $appLogger;
    }

    public function getRegisterWorkers(): array
    {
        return [
            self::WATCHED => 'watched',
        ];
    }

    public function watched(array $data)
    {
        $repository = $this->em->getRepository(Article::class);
        if ($article = $repository->find($data['articleId'])) {
            $article->incWatchCount();
            $this->em->persist($article);
            $this->em->flush();
        }
    }
}
