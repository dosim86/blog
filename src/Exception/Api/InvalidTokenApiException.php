<?php

namespace App\Exception\Api;

use App\Exception\AppException;

class InvalidTokenApiException extends AppException
{
    protected $message = 'Invalid token';
}