<?php

namespace App\Exception\Api;

use App\Exception\AppException;

class InvalidTokenException extends AppException
{
    protected $message = 'Invalid token';
}