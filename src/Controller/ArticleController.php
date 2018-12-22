<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Form\Filter\ArticleFilter;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Route("/article", name="article_list")
     */
    public function search(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginator)
    {
        /** @var Form $filter */
        $filter = $this->createForm(ArticleFilter::class);
        $filter->handleRequest($request);

        if ($filter->isSubmitted()) {
            if ($filter->get('reset')->isClicked()) {
                $filter = $this->createForm(ArticleFilter::class, null);
            } else {
                $collapse = 'show';
            }
        }

        $page = $request->query->getInt('page', 1);
        $qb = $articleRepository->searchArticles($filter->getData());
        $pagination = $paginator->paginate($qb, $page, Article::ITEMS);

        return $this->render('article/search.html.twig', [
            'pagination' => $pagination,
            'filter' => $filter->createView(),
            'collapse' => $collapse ?? ''
        ]);
    }

    /**
     * @Route("/article/tag/{name}", name="article_by_tag")
     */
    public function articleByTag(
        $name,
        Request $request,
        TagRepository $tagRepository,
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator
    ) {
        $page = $request->query->getInt('page', 1);
        $qb = $articleRepository->searchArticles([
            'tags' => new ArrayCollection($tagRepository->findBy(['name' => $name]))
        ]);
        $pagination = $paginator->paginate($qb, $page, Article::ITEMS);

        $filter = $this->createForm(ArticleFilter::class);
        return $this->render('article/search.html.twig', [
            'pagination' => $pagination,
            'filter' => $filter->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/article/add", name="article_add")
     */
    public function add(Request $request)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());

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
            $em = $this->getDoctrine()->getManager();

            /** @var Comment $comment */
            $comment = $commentForm->getData();
            $comment->setOwner($this->getUser());
            $comment->setArticle($article);
            $em->persist($comment);

            $article->incCommentCount();
            $em->persist($article);

            $em->flush();

            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView()
        ]);
    }
}
