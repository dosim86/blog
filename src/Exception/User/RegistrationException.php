<?php

namespace App\Exception\User;

use App\Exception\AppException;

class RegistrationException extends AppException
{
    protected $message = 'User already exists';
}