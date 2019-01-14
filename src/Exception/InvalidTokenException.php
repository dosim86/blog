<?php

namespace App\Exception;

class InvalidTokenException extends AppException
{
    protected $message = 'Invalid token';
}