<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;

class ArticleControllerTest extends AppWebTestCase
{
    public function provideUrls()
    {
        return [
            ['/'],
            ['/user/'],
            ['/article'],
        ];
    }

    /**
     * @dataProvider provideUrls
     */
    public function testAllPublicPagesAreSuccessful($url)
    {
        self::$client->request('GET', $url);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());
    }

    public function testLikeArticle()
    {
        self::$client->request('GET', '/api/article/like/185', [], [], [
            'HTTP_X-AUTH-TOKEN' => 'uSmAAIopfOkwet0LfcVAAHKDjHYu8URyJ-pYl2GO9jA'
        ]);
        $response = json_decode(self::$client->getResponse()->getContent(), true);

        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('type', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertTrue($response['type'] === 'success');
        $this->assertTrue($response['message'] === 'Article is liked');
    }
}