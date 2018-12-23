<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class UserEvent extends Event
{
    const RANK = 'app.user.rank';

    private $request;

    private $userId;

    public function __construct(Request $request, $userId)
    {
        $this->request = $request;
        $this->userId = $userId;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}