<?php

namespace App\MessageHandler;

use App\Message\ResolutionProposedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotifyComplainantResolutionProposalHandler
{
    public function __invoke(ResolutionProposedMessage $message): void
    {
    }
}
