<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;

class ArticleControllerTest extends AppWebTestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testAllPublicPagesAreSuccessful($url)
    {
        $this->markTestIncomplete('This test is not implemented yet.');

        self::$client->request('GET', $url);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());
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