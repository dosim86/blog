<?php

namespace App\Lib;

class Helper
{
    public static function sanitizeJs(string $string)
    {
        return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $string);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function randomHash32()
    {
        return md5(random_bytes(32));
    }
}