<?php

namespace App\Controller;

use App\Entity\Article;
use App\Event\ArticleEvent;
use App\Form\Filter\ArticleFilter;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    public function show(Request $request, ArticleRepository $repository, EventDispatcherInterface $dispatcher)
    {
        if (empty($article = $repository->getArticleBySlug($request->get('slug')))) {
            throw $this->createNotFoundException('Article not found');
        }

        $event = new ArticleEvent($request, $article);
        $dispatcher->dispatch(ArticleEvent::WATCH, $event);

        $response = $this->forward(CommentController::class.'::add', [
            'article' => $article
        ]);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'htmlAddComment' => $response->getContent()
        ]);
    }
}
