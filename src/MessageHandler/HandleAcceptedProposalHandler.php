<?php

namespace App\MessageHandler;

use App\Message\InternalDecisionMadeMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class HandleAcceptedProposalHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(InternalDecisionMadeMessage $message): void
    {
        $this->logger->info(sprintf(
            'Internal decision made for complaint %s: %s',
            $message->complaintId,
            $message->decisionId
        ));
    }
}
