<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Repository\Elastic\ArticleElasticRepository;
use App\Service\ArticleManager;
use App\Tests\AppWebTestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ArticleManagerTest extends AppWebTestCase
{
    /**
     * @var ArticleManager
     */
    protected $manager;

    protected function setUp()
    {
        parent::setUp();

        $formFactory = self::$container->get('form.factory');
        $paginator = self::$container->get('knp_paginator');
        $articleRepository = self::$em->getRepository(Article::class);
        $articleElastic = $this->createMock(ArticleElasticRepository::class);

        $this->manager = new ArticleManager(
            $formFactory,
            $paginator,
            $articleRepository,
            $articleElastic
        );
    }

    public function testHandlingRequestAndGettingFilter()
    {
        $filter = $this->manager->getHandledFilter(new Request());

        $this->assertNotNull($filter);
        $this->assertEmpty($filter->getData());
        $this->assertInstanceOf(Form::class, $filter);

        return $filter;
    }

    /**
     * @depends testHandlingRequestAndGettingFilter
     */
    public function testFilterNotHaveUnknownProperty(Form $filter)
    {
        $properties = [
            'query',
            'queryfor',
            'period',
            'tags',
            'category',
            'search',
            'reset',
            'author',
            '_token',
        ];
        $children = $filter->createView()->children;

        foreach ($children as $key => $child) {
            $this->assertContains($key, $properties);
        }
    }

    /**
     * @depends testHandlingRequestAndGettingFilter
     */
    public function testGettingPaginationInSearch(Form $filter)
    {
        $pagination = $this->manager->search($filter, 1);

        $this->assertCount(0, $pagination);
        $this->assertContainsOnlyInstancesOf(Article::class, $pagination);
    }
}