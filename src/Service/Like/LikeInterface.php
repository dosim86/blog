<?php

namespace App\Service\Like;

interface LikeInterface
{
    public function setUserId(int $userId);

    public function setTarget(LikeableInterface $target);

    public function setValue(int $value);

    public function getValue();
}