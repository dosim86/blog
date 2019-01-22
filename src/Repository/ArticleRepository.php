<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use App\Form\Filter\ArticleFilter;
use App\Repository\Elastic\ArticleElasticRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private $articleElastic;

    public function __construct(RegistryInterface $registry, ArticleElasticRepository $articleElastic)
    {
        $this->articleElastic = $articleElastic;

        parent::__construct($registry, Article::class);
    }

    private function buildQueryByFilter(QueryBuilder $qb, Form $filter)
    {
        if ($filter->isEmpty()) {
            return $qb;
        }

        if ($filter['query']->getData()) {
            $articleIds = $this->articleElastic->getIds([
                'query' => $filter['query']->getData(),
                'queryfor' => $filter['queryfor']->getData(),
            ]);

            $qb->andWhere('a.id in (:a_ids)')
                ->setParameter('a_ids', $articleIds);
        }

        switch ($filter['period']->getData()) {
            case ArticleFilter::PERIOD_TODAY:
                $qb->andWhere('a.createdAt >= :a_createdAt')
                    ->setParameter('a_createdAt', new \DateTime());
                break;
            case ArticleFilter::PERIOD_LASTWEEK:
                $qb->andWhere('a.createdAt >= :a_createdAt')
                    ->setParameter('a_createdAt', new \DateTime('-1 week'));
                break;
            case ArticleFilter::PERIOD_LASTMONTH:
                $qb->andWhere('a.createdAt >= :a_createdAt')
                    ->setParameter('a_createdAt', new \DateTime('-1 month'));
                break;
            case ArticleFilter::PERIOD_LASTYEAR:
                $qb->andWhere('a.createdAt >= :a_createdAt')
                    ->setParameter('a_createdAt', new \DateTime('-1 year'));
                break;
        }

        if ($author = $filter['author']->getData()) {
            $qb->andWhere('a.author = :a_author')
                ->setParameter('a_author', $author);
        }

        /** @var ArrayCollection $tags */
        if (($tags = $filter['tags']->getData()) && !$tags->isEmpty()) {
            $qb->andWhere('t IN (:a_tags)')
                ->setParameter('a_tags', $tags);
        }

        if ($category = $filter['category']->getData()) {
            $qb->andWhere('a.category = :a_category')
                ->setParameter('a_category', $category);
        }

        return $qb;
    }

    public function createSearchQuery(Form $filter): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('t')
            ->addSelect('au')
            ->leftJoin('a.author', 'au')
            ->leftJoin('a.tags', 't')
            ->orderBy('a.createdAt', 'DESC')
        ;

        return $this->buildQueryByFilter($qb, $filter);
    }

    public function getUserArticles(User $user)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('c')
            ->addSelect('t')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('a.tags', 't')
            ->orderBy('a.createdAt', 'DESC');

        if ($user) {
            $qb->andWhere('a.author = :a_author')
                ->setParameter('a_author', $user);
        }

        return $qb;
    }

    /**
     * @param $slug
     * @return Article|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getArticleBySlug($slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c')
            ->addSelect('ch')
            ->addSelect('o')
            ->addSelect('t')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('c.children', 'ch')
            ->leftJoin('c.owner', 'o')
            ->leftJoin('a.tags', 't')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param User $author
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalLikeCount(User $author): int
    {
        return $this->createQueryBuilder('a')
            ->select('SUM(a.likeCount)')
            ->andWhere('a.author = :a_author')
            ->setParameter('a_author', $author)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
