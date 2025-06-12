<?php

namespace App\MessageHandler;

use App\Message\ComplaintClosedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendComplaintClosureNotificationHandler
{
    public function __invoke(ComplaintClosedMessage $message): void
    {
    }
}
