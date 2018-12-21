<?php

namespace App\Helper;

class Html
{
    public static function sanitizeJs(string $string)
    {
        return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $string);
    }
}