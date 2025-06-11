<?php

namespace App\MessageHandler;

use App\Message\ComplaintReceivabilityVerifiedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendReceivabilityDecisionNotificationHandler
{
    public function __invoke(ComplaintReceivabilityVerifiedMessage $message)
    {
    }
}
