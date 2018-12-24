<?php

namespace App\Lib\Like;

interface LikeableInterface
{
    public static function getLikeClass();

    public function getId();

    public function incLikeCount();

    public function decLikeCount();

    public function incDislikeCount();

    public function decDislikeCount();
}