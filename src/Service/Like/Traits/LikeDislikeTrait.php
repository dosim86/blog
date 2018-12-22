<?php

namespace App\Service\Like\Traits;

use Doctrine\ORM\Mapping as ORM;

trait LikeDislikeTrait
{
    /**
     * @ORM\Column(type="integer")
     */
    private $likes;

    /**
     * @ORM\Column(type="integer")
     */
    private $dislikes;

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function incLike()
    {
        $this->likes++;
        return $this;
    }

    public function decLike()
    {
        $this->likes--;
        return $this;
    }

    public function incDislike()
    {
        $this->dislikes++;
        return $this;
    }

    public function decDislike()
    {
        $this->dislikes--;
        return $this;
    }
}