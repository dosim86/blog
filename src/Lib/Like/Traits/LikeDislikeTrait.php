<?php

namespace App\Lib\Like\Traits;

use Doctrine\ORM\Mapping as ORM;

trait LikeDislikeTrait
{
    /**
     * @ORM\Column(type="integer")
     */
    private $likeCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $dislikeCount = 0;

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function getDislikeCount(): ?int
    {
        return $this->dislikeCount;
    }

    public function incLikeCount()
    {
        $this->likeCount++;
        return $this;
    }

    public function decLikeCount()
    {
        !$this->likeCount ?: $this->likeCount--;
        return $this;
    }

    public function incDislikeCount()
    {
        $this->dislikeCount++;
        return $this;
    }

    public function decDislikeCount()
    {
        !$this->dislikeCount ?: $this->dislikeCount--;
        return $this;
    }
}