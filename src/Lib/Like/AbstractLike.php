<?php

namespace App\Lib\Like;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractLike implements LikeInterface
{
    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    protected $value;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $targetId;

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param int $targetId
     */
    public function setTargetId(int $targetId)
    {
        $this->targetId = $targetId;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}