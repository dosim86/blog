<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getWithQueryBuilder($q)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('c')
            ->leftJoin('a.comments', 'c')
            ->orderBy('a.id', 'ASC');

        if ($q) {
            $qb->andWhere('a.title LIKE :a_title')
                ->setParameter('a_title', '%'.$q.'%');
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
