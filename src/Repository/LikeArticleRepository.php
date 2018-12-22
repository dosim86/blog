<?php

namespace App\Repository;

use App\Entity\LikeArticle;
use App\Service\Like\LikeableInterface;
use App\Service\Like\LikeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LikeArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeArticle[]    findAll()
 * @method LikeArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeArticleRepository extends ServiceEntityRepository implements LikeRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LikeArticle::class);
    }

    /**
     * @param LikeableInterface $entity
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLikeCountByEntity(LikeableInterface $entity): int
    {
        return $this->createQueryBuilder('la')
            ->select('COUNT(la)')
            ->andWhere('la.targetId = :targetId')
            ->andWhere('la.value = 1')
            ->setParameter('targetId', $entity->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param LikeableInterface $entity
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDislikeCountByEntity(LikeableInterface $entity): int
    {
        return $this->createQueryBuilder('la')
            ->select('COUNT(la)')
            ->andWhere('la.targetId = :targetId')
            ->andWhere('la.value = -1')
            ->setParameter('targetId', $entity->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param LikeableInterface $entity
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFullSeparatedCount(LikeableInterface $entity): array
    {
        return $this->createQueryBuilder('la')
            ->select('IFNULL(SUM(IFELSE(la.value=1,1,0)),0) as likes')
            ->addSelect('IFNULL(SUM(IFELSE(la.value=-1,1,0)),0) as dislikes')
            ->andWhere('la.targetId = :targetId')
            ->setParameter('targetId', $entity->getId())
            ->getQuery()
            ->getSingleResult()
        ;
    }
}
