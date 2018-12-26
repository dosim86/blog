<?php

namespace App\Lib\Traits;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait ManagerTrait
{
    /** @var ContainerInterface */
    private $container;

    public function getEntityManager()
    {
        return $this->container
            ->get('doctrine.orm.default_entity_manager');
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