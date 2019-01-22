<?php

namespace App\Repository\Elastic;

use Psr\Container\ContainerInterface;
use FOS\ElasticaBundle\Elastica\Index;

abstract class ElasticRepository
{
    /**
     * @var Index
     */
    protected $index;

    public function __construct(ContainerInterface $container)
    {
        $this->index = $container->get('fos_elastica.index.blog');
    }

    abstract public function getType();
}