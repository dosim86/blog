<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
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
        $queryBuilder = $articleRepository->getWithQueryBuilder();
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);

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
    public function show(Article $article)
    {
        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);
    }
}
