<?php

namespace App\Service;

use App\Entity\Article;
use App\Form\Filter\ArticleFilter;
use App\Repository\ArticleRepository;
use App\Repository\Elastic\ArticleElasticRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ArticleManager
{
    private $form;

    private $paginator;

    private $articleElastic;

    private $articleRepository;

    public function __construct(
        FormFactoryInterface $form,
        PaginatorInterface $paginator,
        ArticleRepository $articleRepository,
        ArticleElasticRepository $articleElastic
    ) {
        $this->form = $form;
        $this->articleElastic = $articleElastic;
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
    }

    public function getHandledFilter(Request $request): Form
    {
        /** @var Form $filter */
        $filter = $this->form->create(ArticleFilter::class);
        $filter->handleRequest($request);

        if ($filter->isSubmitted()) {
            if ($filter->get('reset')->isClicked()) {
                $filter = $this->form->create(ArticleFilter::class);
            }
        }

        return $filter;
    }

    public function search(Form $filter, $page, $limit = Article::ITEMS)
    {
        $searchQuery = $this->articleRepository->createSearchQuery($filter);
        return $this->paginate($searchQuery, $page, $limit);
    }

    public function refreshElastic(Article $article)
    {
        $type = $this->articleElastic->getType();
        $document = $type->getDocument($article->getId());

        $document->setData([
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
        ]);
        $type->updateDocument($document);
    }

    private function paginate(QueryBuilder $qb, $page = 1, $limit = 10)
    {
        return $this->paginator->paginate($qb, $page, $limit);
    }
}