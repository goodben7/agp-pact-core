<?php

namespace App\Factory;

use App\Entity\Notification;
use App\Entity\NotificationTemplate;

class NotificationFactory
{
    /**
     * Creates a Notification object from a NotificationTemplate.
     *
     * @param NotificationTemplate $template The notification template to use.
     * @param string $recipient The recipient of the notification (e.g., email, phone, user ID).
     * @param string $recipientType The type of the recipient (e.g., 'email', 'user', 'phone').
     * @param array|null $data Optional data to be included in the notification.
     * @param string|null $sentVia Optional method by which the notification was sent (e.g., 'system', 'gmail').
     * @return Notification
     * @throws \InvalidArgumentException If the recipient type is invalid.
     */
    public function createFromTemplate(
        NotificationTemplate $template,
        string $recipient,
        string $recipientType,
        ?array $data = null,
        ?string $sentVia = null
    ): Notification {
        $notification = new Notification();

        // Map properties from NotificationTemplate to Notification
        $notification->setType($template->getType());
        $notification->setSubject($data['subject'] ?? $template->getSubject()); // Use template subject if not provided
        // The content from template becomes the body of the notification
        $notification->setBody($data['content'] ?? ''); // Ensure body is not null

        // Set recipient information
        $notification->setRecipient($recipient);

        // Validate recipient type
        if (!in_array($recipientType, Notification::getRecipientTypeChoices())) {
            throw new \InvalidArgumentException(sprintf('Invalid recipient type "%s" provided.', $recipientType));
        }
        $notification->setRecipientType($recipientType);

        // Set optional data and sentVia
        if ($data !== null) {
            $notification->setData($data);
        }
        if ($sentVia !== null) {
            $notification->setSentVia($sentVia);
        }

        // createdAt is automatically set in Notification's constructor

        return $notification;
    }
}
