<?php

namespace App\Exception\User;

use App\Exception\AppException;

class UnknownUserException extends AppException
{
    protected $message = 'Unknown user';
}