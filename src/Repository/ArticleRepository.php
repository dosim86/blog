<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use App\Form\Filter\ArticleFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    private function buildQueryByFilter(QueryBuilder $qb, $filter)
    {
        if (empty($filter)) {
            return $qb;
        }

        if ($query = $filter['query'] ?? null) {
            switch ($filter['queryfor']) {
                case ArticleFilter::QUERYFOR_TITLE:
                    $qb->andWhere('a.title LIKE :a_query')
                        ->setParameter('a_query', '%'.$query.'%');
                    break;
                case ArticleFilter::QUERYFOR_CONTENT:
                    $qb->andWhere('a.content LIKE :a_query')
                        ->setParameter('a_query', '%'.$query.'%');
                    break;
                case ArticleFilter::QUERYFOR_BOTH:
                    $qb->andWhere('a.title LIKE :a_query OR a.content LIKE :a_query')
                        ->setParameter('a_query', '%'.$query.'%');
                    break;
            }
        }

        switch ($filter['period'] ?? null) {
            case ArticleFilter::PERIOD_TODAY:
                dd('1');
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

        if ($author = $filter['author'] ?? null) {
            $qb->andWhere('a.author = :a_author')
                ->setParameter('a_author', $author);
        }

        /** @var ArrayCollection $tags */
        if (($tags = $filter['tags'] ?? null) && !$tags->isEmpty()) {
            $qb->andWhere('t IN (:a_tags)')
                ->setParameter('a_tags', $tags);
        }

        if ($category = $filter['category'] ?? null) {
            $qb->andWhere('a.category IN (:a_category)')
                ->setParameter('a_category', $category);
        }

        return $qb;
    }

    public function searchArticles($filter)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('c')
            ->addSelect('t')
            ->addSelect('au')
            ->leftJoin('a.comments', 'c')
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
            ->leftJoin('a.comments', 'c')
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
            ->addSelect('o')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('c.owner', 'o')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
