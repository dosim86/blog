<?php

namespace App\Entity;

use App\Service\Like\LikeableInterface;
use App\Service\Like\LikeInterface;
use App\Service\Like\Traits\ValueTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeCommentRepository")
 */
class LikeComment implements LikeInterface
{
    use ValueTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    protected $userId;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Comment", inversedBy="likes")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id", nullable=false)
     */
    protected $target;

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param LikeableInterface $target
     */
    public function setTarget(LikeableInterface $target)
    {
        $this->target = $target;
    }
}
