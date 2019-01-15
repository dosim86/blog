<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\BookmarkArticle;
use App\Event\UserEvent;
use App\Service\LikeManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
     * @Route("/like/{id<\d+>}", name="api_article_like")
     * @throws \Exception
     */
    public function like(
        Request $request,
        Article $article,
        LikeManager $likeManager,
        EventDispatcherInterface $dispatcher
    ) {
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
    }

    /**
     * @Route("/dislike/{id<\d+>}", name="api_article_dislike")
     * @throws \Exception
     */
    public function dislike(
        Request $request,
        Article $article,
        LikeManager $likeManager,
        EventDispatcherInterface $dispatcher
    ) {
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
    }

    /**
     * @Route("/bookmark/{id<\d+>}", name="api_article_bookmark", options={"expose"=true})
     */
    public function bookmark(Article $article)
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
        }
    }
}