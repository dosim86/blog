<?php

namespace App\Exception\Like;

use App\Exception\AppException;

class LikeException extends AppException
{
    protected $message = 'Fail like object';
}