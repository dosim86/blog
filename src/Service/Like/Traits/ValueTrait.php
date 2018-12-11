<?php

namespace App\Service\Like\Traits;

trait ValueTrait
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $value;

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