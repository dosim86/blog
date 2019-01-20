<?php

namespace App\Exception\Worker;

use App\Exception\AppException;

class WorkerNotFoundException extends AppException
{
    protected $message = 'Worker not found';
}