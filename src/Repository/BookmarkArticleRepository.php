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

    public function getArticlesFromBookmarkByUser(User $user)
    {
        return $this->_em->createQueryBuilder()
            ->select('a')
            ->addSelect('au')
            ->addSelect('t')
            ->from(Article::class, 'a')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.author', 'au')
            ->join(BookmarkArticle::class, 'ba', Join::WITH, 'a = ba.article')
            ->andWhere('ba.user = :ba_user')
            ->setParameter('ba_user', $user)
        ;
    }
}
