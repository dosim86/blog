<?php

namespace App\Lib\Traits;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait ManagerTrait
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @return EntityManager
     * @throws \Exception
     */
    public function getEntityManager()
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        if (!$em->isOpen()) {
            $em = $em->create($em->getConnection(), $em->getConfiguration());
        }
        return $em;
    }

    public function getDoctrine()
    {
        return $this->container
            ->get('doctrine');
    }

    public function getParameter($name)
    {
        return $this->container
            ->getParameter($name);
    }

    public function generateUrl($route, $parameters = [])
    {
        return $this->container
            ->get('router')
            ->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getPasswordEncoder()
    {
        return $this->container
            ->get('security.password_encoder');
    }

    public function renderView($view, $parameters = [])
    {
        return $this->container
            ->get('twig')
            ->render($view, $parameters);
    }
}