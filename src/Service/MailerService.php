<?php

namespace App\Service;

use App\Exception\Mail\MailException;

class MailerService
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param array $options
     * @return bool
     * @throws MailException
     */
    public function send(array $options)
    {
        if (empty($subject = $options['subject'] ?? '')
            || empty($from = $options['from'] ?? '')
            || empty($to = $options['to'] ?? '')
            || empty($text = $options['text'] ?? '')
        ) {
            throw new MailException();
        }

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($text, 'text/html')
        ;
        return $this->mailer->send($message);
    }
}