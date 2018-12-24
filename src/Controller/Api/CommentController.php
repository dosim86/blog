<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Exception\Api\FailApiException;
use App\Exception\Api\InvalidTokenApiException;
use App\Exception\Like\FailLikeException;
use App\Service\Like\LikeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="api_comment_like")
     * @throws \Exception
     */
    public function like(Comment $comment, LikeManager $likeManager)
    {
        try {
            $likeManager->like($comment, $this->getUser());
            $data = [
                'likes' => $comment->getLikeCount(),
                'dislikes' => $comment->getDislikeCount(),
            ];

            return $this->json([
                'type' => 'success',
                'message' => 'Comment is liked',
                'data' => $data
            ]);
        } catch (FailLikeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new FailApiException();
        }
    }

    /**
     * @Route("/dislike/{id}", name="api_comment_dislike")
     * @throws \Exception
     */
    public function dislike(Comment $comment, LikeManager $likeManager)
    {
        try {
            $likeManager->dislike($comment, $this->getUser());
            $data = [
                'likes' => $comment->getLikeCount(),
                'dislikes' => $comment->getDislikeCount(),
            ];

            return $this->json([
                'type' => 'success',
                'message' => 'Comment is disliked',
                'data' => $data
            ]);
        } catch (FailLikeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new FailApiException();
        }
    }
}
