<?php

namespace App\Exception\Like;

use App\Exception\AppException;

class UnknownLikeClassException extends AppException
{
    protected $message = 'Unknown like class';
}