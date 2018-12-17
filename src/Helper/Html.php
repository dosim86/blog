<?php

namespace App\Helper;

class Html
{
    public static function sanititzeJs(string $string)
    {
        return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $string);
    }
}