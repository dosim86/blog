<?php

namespace App\Lib\Like;

use App\Entity\User;

interface LikeManagerInterface
{
    public function like(LikeableInterface $entity, User $user);

    public function dislike(LikeableInterface $entity, User $user);
}