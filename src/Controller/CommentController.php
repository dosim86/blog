<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     */
    public function add(Article $article, RequestStack $requestStack)
    {
        $request = $requestStack->getMasterRequest();
        $form = $this->createForm(CommentType::class);

        if ($slug = $request->get('slug', null)) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Comment $comment */
                $comment = $form->getData();
                $comment->setOwner($this->getUser());
                $comment->setArticle($article->incCommentCount());

                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                $em->refresh($article);

                $this->addFlash('success', 'Comment is added');
                $form = $this->createForm(CommentType::class);
            }
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
