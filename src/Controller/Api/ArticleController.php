<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\BookmarkArticle;
use App\Exception\Api\FailApiException;
use App\Exception\Like\FailLikeException;
use App\Service\Like\LikeManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function like(Article $article, LikeManager $likeManager)
    {
        try {
            $likeManager->like($article, $this->getUser());
            $data = [
                'likes' => $article->getLikeCount(),
                'dislikes' => $article->getDislikeCount(),
            ];

            return $this->json([
                'type' => 'success',
                'message' => 'Article is liked',
                'data' => $data,
            ]);
        } catch (FailLikeException $e) {
            throw new FailApiException();
        }
    }

    /**
     * @Route("/dislike/{id}", name="api_article_dislike")
     * @throws \Exception
     */
    public function dislike(Article $article, LikeManager $likeManager)
    {
        try {
            $likeManager->dislike($article, $this->getUser());
            $data = [
                'likes' => $article->getLikeCount(),
                'dislikes' => $article->getDislikeCount(),
            ];

            return $this->json([
                'type' => 'success',
                'message' => 'Article is disliked',
                'data' => $data,
            ]);
        } catch (FailLikeException $e) {
            throw new FailApiException();
        }
    }

    /**
     * @Route("/bookmark/{id}", name="api_article_bookmark")
     * @throws \Exception
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
        } catch (\Exception $e) {
            throw new FailApiException();
        }
    }
}