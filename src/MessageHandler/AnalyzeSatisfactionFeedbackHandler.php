<?php

namespace App\MessageHandler;

use App\Message\SatisfactionFollowedUpMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AnalyzeSatisfactionFeedbackHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(SatisfactionFollowedUpMessage $message): void
    {
        $this->logger->info(sprintf(
            'Satisfaction feedback analyzed for complaint %s. Feedback: %s',
            $message->complaintId,
            $message->satisfactionResultId
        ));
    }
}
