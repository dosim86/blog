<?php

namespace App\Service\Like;

interface LikeableInterface
{
    public static function getLikeClass();

    public function getId();

    public function incLike();

    public function decLike();

    public function incDislike();

    public function decDislike();
}