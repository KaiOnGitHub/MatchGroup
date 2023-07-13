<?php

namespace App\Service\Notification;

use App\Entity\PlayerEntity;
use App\Service\Notification\NotificationServiceInterface;use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationService implements NotificationServiceInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function sendNotification(PlayerEntity $recipient, string $subject, string $message): void
    {
        $toMail = 'kai.koenig@pm.me';
        // TODO: Actually get mail from player
        $this->sendEmail($this->mailer, $toMail, $subject, $message);
    }

    private function sendEmail(MailerInterface $mailer, string $recipient, string $subject, string $message): void
    {
        $email = (new Email())
            ->from('from@example.org')
            ->to($recipient)
            ->subject($subject)
            ->text($message)
            ->html($message);

        $mailer->send($email);
    }
}
