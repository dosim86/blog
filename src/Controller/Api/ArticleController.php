<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\BookmarkArticle;
use App\Event\UserEvent;
use App\Exception\Api\ApiException;
use App\Service\LikeManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="api_article_like")
     * @throws \Exception
     */
    public function like(
        Request $request,
        Article $article,
        LikeManager $likeManager,
        LoggerInterface $appLogger,
        EventDispatcherInterface $dispatcher
    ) {
        try {
            $likeManager->like($article, $this->getUser());
            $data = [
                'likes' => $article->getLikeCount(),
                'dislikes' => $article->getDislikeCount(),
            ];

            $event = new UserEvent($request, $article->getAuthor());
            $dispatcher->dispatch(UserEvent::RANK, $event);

            return $this->json([
                'type' => 'success',
                'message' => 'Article is liked',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            $appLogger->error($e->getMessage());
            throw new ApiException();
        }
    }

    /**
     * @Route("/dislike/{id}", name="api_article_dislike")
     * @throws \Exception
     */
    public function dislike(
        Request $request,
        Article $article,
        LikeManager $likeManager,
        LoggerInterface $appLogger,
        EventDispatcherInterface $dispatcher
    ) {
        try {
            $likeManager->dislike($article, $this->getUser());
            $data = [
                'likes' => $article->getLikeCount(),
                'dislikes' => $article->getDislikeCount(),
            ];

            $event = new UserEvent($request, $article->getAuthor());
            $dispatcher->dispatch(UserEvent::RANK, $event);

            return $this->json([
                'type' => 'success',
                'message' => 'Article is disliked',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            $appLogger->error($e->getMessage());
            throw new ApiException();
        }
    }

    /**
     * @Route("/bookmark/{id}", name="api_article_bookmark")
     * @throws \Exception
     */
    public function bookmark(Article $article, LoggerInterface $appLogger)
    {
        try {
            $article->incBookmarkCount();

            $bookmark = new BookmarkArticle();
            $bookmark->setUser($this->getUser());
            $bookmark->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($bookmark);
            $em->flush();

            return $this->json([
                'type' => 'success',
                'message' => 'Article is added to bookmark',
                'data' => $article->getBookmarkCount(),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            return $this->json([
                'type' => 'info',
                'message' => 'You already added the article'
            ]);
        } catch (\Exception $e) {
            $appLogger->error($e->getMessage());
            throw new ApiException();
        }
    }
}