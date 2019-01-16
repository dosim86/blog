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

    /**
     * @return string
     * @throws \Exception
     */
    public static function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}