<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\BookmarkArticle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BookmarkArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookmarkArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookmarkArticle[]    findAll()
 * @method BookmarkArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookmarkArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BookmarkArticle::class);
    }

    /**
     * @param Article $article
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBookmarkCountForArticle(Article $article): int
    {
        return $this->createQueryBuilder('ba')
            ->select('COUNT(ba.user)')
            ->andWhere('ba.article = :ba_article')
            ->setParameter('ba_article', $article)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getArticlesFromBookmarkByUser(User $user)
    {
        return $this->_em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->join(BookmarkArticle::class, 'ba', Join::WITH, 'a = ba.article')
            ->andWhere('ba.user = :ba_user')
            ->setParameter('ba_user', $user)
        ;
    }
}
