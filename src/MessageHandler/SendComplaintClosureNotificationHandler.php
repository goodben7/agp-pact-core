<?php

namespace App\MessageHandler;

use App\Message\ComplaintClosedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendComplaintClosureNotificationHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ComplaintClosedMessage $message): void
    {
        $this->logger->info('Complaint closed', [
            'complaintId' => $message->getComplaintId(),
            'closureReason' => $message->getClosureReason(),
        ]);
    }
}
