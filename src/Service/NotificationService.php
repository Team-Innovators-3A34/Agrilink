<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailNotification( string $subject, string $body): void
    {
        $email = (new Email())
            ->from('no-reply@tonapp.com')
            ->to('abouzaouada@gmail.com')
            ->subject($subject)
            ->text($body);

        $this->mailer->send($email);
    }
}