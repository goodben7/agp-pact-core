<?php

namespace App\MessageHandler;

use App\Message\ComplaintEscalatedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotifyCIHandler
{

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ComplaintEscalatedMessage $message): void
    {
        $this->logger->info(sprintf(
            'Complaint %s escalated to %s. Notification process triggered by middleware.',
            $message->complaintId,
            $message->escalationLevelId
        ));
    }
}
