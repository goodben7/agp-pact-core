<?php

namespace App\MessageHandler;

use App\Message\ComplaintEscalatedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotifyCIHandler
{
    public function __invoke(ComplaintEscalatedMessage $message): void
    {
    }
}
