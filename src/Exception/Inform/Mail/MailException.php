<?php

namespace App\Exception\Inform\Mail;

use App\Exception\AppException;

class MailException extends AppException
{
    protected $message = 'Invalid mailer params';
}