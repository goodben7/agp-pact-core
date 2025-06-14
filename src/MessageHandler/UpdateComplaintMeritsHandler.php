<?php

namespace App\MessageHandler;

use App\Message\ComplaintClassifiedAssignedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateComplaintMeritsHandler
{

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(ComplaintClassifiedAssignedMessage $message): void
    {
        $this->logger->info(sprintf(
            'Complaint %s assigned to user %s. Notification process triggered by middleware.',
            $message->getComplaintId(),
            $message->getAssignedToUserId()
        ));
    }
}
