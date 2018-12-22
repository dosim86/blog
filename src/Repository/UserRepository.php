<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAuthorsMatchTo($authorName)
    {
        return $this->createQueryBuilder('u')
            ->select('u.email AS id, u.firstname AS text')
            ->andWhere('u.firstname LIKE :u_firstname')
            ->setParameter('u_firstname', $authorName.'%')
            ->setMaxResults(User::AUTHOR_ITEM)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function getAuthors($authorName = '')
    {
        $qb = $this->createQueryBuilder('u');

        if ($authorName) {
            $qb->andWhere('u.firstname LIKE :u_firstname')
                ->setParameter('u_firstname', $authorName.'%');
        }

        return $qb;
    }
}
