<?php

namespace App\MessageHandler;

use App\Message\AssignedMessage;
use App\Message\ComplaintClassifiedAssignedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotifyAssignedCompanyHandler
{

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(AssignedMessage $message): void
    {
        $this->logger->info(sprintf(
            'Complaint %s assigned to company %s. Notification process triggered by middleware.',
            $message->getComplaintId(),
            $message->getAssignedToCompanyId()
        ));
    }
}
