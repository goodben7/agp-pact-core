<?php

namespace App\Manager;

use App\Entity\Notification;
use App\Message\SendNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class NotificationManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function create(
        string  $type,
        string  $recipientType,
        string  $recipient,
        string  $body,
        ?string $subject = null,
        ?array  $data = null,
        ?string $sentVia = Notification::SENT_VIA_SYSTEM
    ): Notification
    {
        $notification = (new Notification())
            ->setType($type)
            ->setRecipientType($recipientType)
            ->setRecipient($recipient)
            ->setBody($body)
            ->setSubject($subject)
            ->setData($data)
            ->setSentVia($sentVia);

        $this->em->persist($notification);
        $this->em->flush();

        $this->bus->dispatch(new SendNotificationMessage($notification));

        return $notification;
    }
}