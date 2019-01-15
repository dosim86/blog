<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Service\LikeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/like/{id<\d+>}", name="api_comment_like")
     * @throws \Exception
     */
    public function like(Comment $comment, LikeManager $likeManager)
    {
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
    }

    /**
     * @Route("/dislike/{id<\d+>}", name="api_comment_dislike")
     * @throws \Exception
     */
    public function dislike(Comment $comment, LikeManager $likeManager)
    {
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
    }

    /**
     * @Route("/reply/{id<\d+>}", name="api_comment_reply", options={"expose"=true})
     */
    public function reply(Comment $parentComment, Request $request)
    {
        if (empty($text = $request->get('text', ''))) {
            return $this->json([
                'type' => 'error',
                'message' => 'Empty comment',
            ]);
        }

        $article = $parentComment->getArticle();

        $comment = new Comment();
        $comment->setText($text);
        $comment->setOwner($this->getUser());
        $comment->setArticle($article->incCommentCount());
        $comment->setParent($parentComment);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->json([
            'type' => 'success',
            'message' => 'Comment is added',
        ]);
    }
}
