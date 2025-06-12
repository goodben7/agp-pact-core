<?php

namespace App\MessageHandler;

use App\Message\ComplaintRegisteredMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendComplaintConfirmationEmailHandler
{
    public function __construct(
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(ComplaintRegisteredMessage $message)
    {
        $this->logger->info(sprintf(
            'Complaint registered: %s (email: %s, phone: %s)',
            $message->complaintId,
            $message->complaintEmail,
            $message->complaintPhone
        ));
    }
}
