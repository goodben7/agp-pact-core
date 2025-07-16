<?php

namespace App\MessageHandler;

use App\Message\ComplaintReceivabilityVerifiedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendReceivabilityDecisionNotificationHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ComplaintReceivabilityVerifiedMessage $message)
    {
        $this->logger->info(sprintf(
            'Receivability decision sent for complaint %s: %s',
            $message->complaintId,
            $message->isReceivable
        ));
    }
}
