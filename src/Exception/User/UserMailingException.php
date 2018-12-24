<?php

namespace App\Exception\User;

use App\Exception\AppException;

class UserMailingException extends AppException
{
    protected $message = 'User mailing error';
}