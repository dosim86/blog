<?php

namespace App\Tests\Lib;

use App\Lib\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testSanitizeJavascriptTag()
    {
        $this->assertEquals('<p>text1</p>middletext<b>text2</b>', Helper::sanitizeJs(
            '<p>text1<script>alert(1)</script></p>middletext<b><script>alert(2)</script>text2</b>'
        ));
    }

    public function testRandomHashLengthEqualTo32()
    {
        $tokenLength = strlen(Helper::randomHash32());

        $this->assertEquals(32, $tokenLength);
    }

    public function testGenerateTokenLengthEqualTo32()
    {
        // Helper::generateToken();

        $this->markTestIncomplete('This test is not implemented yet.');
    }
}