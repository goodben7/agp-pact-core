<?php

namespace App\Service;

use App\Contract\NotificationSenderInterface;
use App\Entity\Notification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class MailNotificationSender implements NotificationSenderInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    )
    {
    }

    public function send(Notification $notification): void
    {
        $email = (new Email())
            ->to($notification->getRecipient())
            ->subject($notification->getSubject() ?? 'Vous avez une nouvelle notification')
            ->html($notification->getBody());

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Email service error : ' . $e->getMessage());
        }
    }

    public function support(string $sentVia): bool
    {
        return $sentVia === Notification::SENT_VIA_EMAIL;
    }
}
