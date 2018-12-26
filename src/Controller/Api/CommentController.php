<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Exception\Api\ApiException;
use App\Service\LikeManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function like(Comment $comment, LikeManager $likeManager, LoggerInterface $appLogger)
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
        } catch (\Exception $e) {
            $appLogger->error($e->getMessage());
            throw new ApiException();
        }
    }

    /**
     * @Route("/dislike/{id}", name="api_comment_dislike")
     * @throws \Exception
     */
    public function dislike(Comment $comment, LikeManager $likeManager, LoggerInterface $appLogger)
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
        } catch (\Exception $e) {
            $appLogger->error($e->getMessage());
            throw new ApiException();
        }
    }
}
