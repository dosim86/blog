<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CacheService
{
    private $cache;

    private $doctrineResultCache;

    public function __construct(CacheItemPoolInterface $cache, EntityManagerInterface $manager)
    {
        $this->cache = $cache;
        $this->doctrineResultCache = $manager->getConfiguration()->getResultCacheImpl();
    }

    public static function key($key)
    {
        return md5($key);
    }

    /**
     * @param string $key
     * @param \Closure $callback
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get(string $key, \Closure $callback)
    {
        $cachedItem = $this->cache->getItem(self::key($key));

        if (!$cachedItem->isHit()) {
            $cachedItem->set($callback());
            $this->cache->save($cachedItem);
        }

        return $cachedItem->get();
    }

    /**
     * @param $key
     * @throws InvalidArgumentException
     */
    public function del($key)
    {
        $this->cache->deleteItem($key);
    }

    public function delDoctrineResult($key)
    {
        $this->doctrineResultCache->delete(self::key($key));
    }
}
