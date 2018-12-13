<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Exception\Like\FailLikeException;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Service\Like\LikeManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Route("/article", name="article_index")
     */
    public function index(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginator)
    {
        $page = $request->query->getInt('page', 1);
        $qb = $articleRepository->getWithQueryBuilder($request->get('q'));
        $pagination = $paginator->paginate($qb, $page, 10);

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/article/add", name="article_add")
     */
    public function add(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article is created');

            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create new article'
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @IsGranted("EDIT", subject="article")
     * @Route("/article/edit/{id}", name="article_edit")
     */
    public function edit(Article $article, Request $request)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Article is updated');

            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Update: ' . $article->getTitle()
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_show")
     */
    public function show($slug, Request $request, ArticleRepository $articleRepository)
    {
        $article = $articleRepository->getArticleBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        $commentForm = $this->createForm(CommentType::class, new Comment());
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            /** @var Comment $comment */
            $comment = $commentForm->getData();
            $comment->setOwner($this->getUser());
            $comment->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/article/{id}/like", name="article_like")
     */
    public function like(Article $article, Request $request, LikeManager $likeManager)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        try {
            $likeManager->like($article, $this->getUser());
            $data = $likeManager->getCountAsValue($article);

            return $this->json([
                'type' => 'success',
                'message' => 'Article is liked',
                'data' => $data,
            ]);
        } catch (FailLikeException $e) {
            return $this->json([
                'type' => 'error',
                'message' => 'Sorry, there is a system fault'
            ]);
        }
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/article/{id}/dislike", name="article_dislike")
     */
    public function dislike(Article $article, Request $request, LikeManager $likeManager)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        try {
            $likeManager->dislike($article, $this->getUser());
            $data = $likeManager->getCountAsValue($article);

            return $this->json([
                'type' => 'success',
                'message' => 'Article is disliked',
                'data' => $data,
            ]);
        } catch (FailLikeException $e) {
            return $this->json([
                'type' => 'error',
                'message' => 'Sorry, there is a system fault'
            ]);
        }
    }
}
