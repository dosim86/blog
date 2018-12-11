<?php

namespace App\Exception\Like;

use App\Exception\AppException;

class FailLikeException extends AppException
{
    protected $message = 'Fail like object';
}