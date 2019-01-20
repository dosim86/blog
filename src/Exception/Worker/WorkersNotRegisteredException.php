<?php

namespace App\Exception\Worker;

use App\Exception\AppException;

class WorkersNotRegisteredException extends AppException
{
    protected $message = 'Workers not registered';
}