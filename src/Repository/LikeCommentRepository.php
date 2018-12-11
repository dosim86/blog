<?php

namespace App\Repository;

use App\Entity\LikeComment;
use App\Service\Like\LikeableInterface;
use App\Service\Like\LikeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LikeComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeComment[]    findAll()
 * @method LikeComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeCommentRepository extends ServiceEntityRepository implements LikeRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LikeComment::class);
    }

    /**
     * @param LikeableInterface $entity
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLikeCountByEntity(LikeableInterface $entity): int
    {
        return $this->createQueryBuilder('lc')
            ->select('COUNT(lc)')
            ->andWhere('lc.target = :target')
            ->andWhere('lc.value = 1')
            ->setParameter('target', $entity)
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
        return $this->createQueryBuilder('lc')
            ->select('COUNT(lc)')
            ->andWhere('lc.target = :target')
            ->andWhere('lc.value = -1')
            ->setParameter('target', $entity)
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
    public function getCountAsValue(LikeableInterface $entity): array
    {
        return $this->createQueryBuilder('lc')
            ->select('IFNULL(SUM(IFELSE(lc.value=1,1,0)),0) as likes')
            ->addSelect('IFNULL(SUM(IFELSE(lc.value=-1,1,0)),0) as dislikes')
            ->andWhere('lc.target = :target')
            ->setParameter('target', $entity)
            ->getQuery()
            ->getSingleResult()
        ;
    }
}
