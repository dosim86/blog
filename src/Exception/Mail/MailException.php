<?php

namespace App\Exception\Mail;

use App\Exception\AppException;

class MailException extends AppException
{
    protected $message = 'Invalid mailer params';
}