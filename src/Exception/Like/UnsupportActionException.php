<?php

namespace App\Exception\Like;

use App\Exception\AppException;

class UnsupportActionException extends AppException
{
    protected $message = 'Unsupport like action';
}