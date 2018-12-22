<?php

namespace App\Service\Like;

interface LikeRepositoryInterface
{
    public function getLikeCountByEntity(LikeableInterface $entity): int;

    public function getDislikeCountByEntity(LikeableInterface $entity): int;

    public function getFullSeparatedCount(LikeableInterface $entity): array;
}