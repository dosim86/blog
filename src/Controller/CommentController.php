<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Exception\Like\FailLikeException;
use App\Service\Like\LikeManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/api/comment/{id}/like", name="comment_like")
     */
    public function like(Comment $comment, LikeManager $likeManager)
    {
        try {
            $likeManager->like($comment, $this->getUser());
            $data = $likeManager->getCountAsValue($comment);

            return $this->json([
                'type' => 'success',
                'message' => 'Comment is liked',
                'data' => $data
            ]);
        } catch (FailLikeException $e) {
            return $this->json([
                'type' => 'error',
                'message' => 'Sorry, there is a system fault'
            ]);
        }
    }

    /**
     * @Route("/api/comment/{id}/dislike", name="comment_dislike")
     */
    public function dislike(Comment $comment, LikeManager $likeManager)
    {
        try {
            $likeManager->dislike($comment, $this->getUser());
            $data = $likeManager->getCountAsValue($comment);

            return $this->json([
                'type' => 'success',
                'message' => 'Comment is disliked',
                'data' => $data
            ]);
        } catch (FailLikeException $e) {
            return $this->json([
                'type' => 'error',
                'message' => 'Sorry, there is a system fault'
            ]);
        }
    }
}
