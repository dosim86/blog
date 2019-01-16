<?php

namespace App\Repository;

use App\Entity\Seo;
use App\Service\CacheService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Seo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seo[]    findAll()
 * @method Seo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeoRepository extends ServiceEntityRepository
{
    const SEO_PAGE = 'seo_page_';

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Seo::class);
    }

    /**
     * @param $path
     * @return Seo|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMetaData($path): ?Seo
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isDisabled = false')
            ->andWhere('s.path = :s_path')
            ->setParameter('s_path', $path)
            ->getQuery()
            ->useResultCache(true)
            ->setResultCacheId(CacheService::key(self::SEO_PAGE . $path))
            ->getOneOrNullResult()
        ;
    }
}
