<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = self::createClient();
    }

    /**
     * @dataProvider provideUrls
     */
    public function testAllPublicPagesAreSuccessful($url)
    {
        $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            ['/'],
            ['/article'],
            ['/user/'],
            ['/login'],
            ['/register'],
        ];
    }
}