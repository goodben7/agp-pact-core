<?php

namespace App\MessageHandler;

use App\Message\ResolutionProposedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotifyComplainantResolutionProposalHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ResolutionProposedMessage $message): void
    {
        $this->logger->info(sprintf(
            'Resolution proposed for complaint %s. Notification process triggered by middleware.',
            $message->complaintId
        ));
    }
}
