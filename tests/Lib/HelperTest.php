<?php

namespace App\Tests\Lib;

use App\Lib\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testAdd()
    {
        $tokenLength = strlen(Helper::randomHash32());

        $this->assertEquals(32, $tokenLength);
    }
}