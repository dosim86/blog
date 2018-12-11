<?php

namespace App\Exception\Like;

use App\Exception\AppException;

class NotFoundLikeClassException extends AppException
{
    protected $message = 'Not found like class';
}