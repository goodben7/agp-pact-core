<?php

namespace App\MessageHandler;

use App\Message\ResolutionExecutedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotifyResolutionExecutionCompletionHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ResolutionExecutedMessage $message): void
    {
        $this->logger->info(sprintf(
            'Resolution executed for complaint %s. Notification process triggered by middleware.',
            $message->complaintId
        ));
    }
}
