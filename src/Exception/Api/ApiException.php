<?php

namespace App\Exception\Api;

use App\Exception\AppException;

class ApiException extends AppException
{
    protected $message = 'Sorry, there is a system fault';
}