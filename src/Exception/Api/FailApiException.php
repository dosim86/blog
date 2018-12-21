<?php

namespace App\Exception\Api;

use App\Exception\AppException;

class FailApiException extends AppException
{
    protected $message = 'Sorry, there is a system fault';
}