<?php

namespace App\MessageHandler;

use App\Message\InternalDecisionMadeMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class HandleAcceptedProposalHandler
{
    public function __invoke(InternalDecisionMadeMessage $message): void
    {
    }
}
