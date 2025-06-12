<?php

namespace App\MessageHandler;

use App\Message\ComplaintRegisteredMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendComplaintConfirmationEmailHandler
{
    public function __invoke(ComplaintRegisteredMessage $message)
    {
    }
}
