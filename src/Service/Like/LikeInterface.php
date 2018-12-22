<?php

namespace App\Service\Like;

interface LikeInterface
{
    public function setUserId(int $userId);

    public function setTargetId(int $targetId);

    public function setValue(int $value);

    public function getValue();
}