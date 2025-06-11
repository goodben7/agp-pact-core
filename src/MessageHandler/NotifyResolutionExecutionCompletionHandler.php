<?php

namespace App\MessageHandler;

use App\Message\ResolutionExecutedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotifyResolutionExecutionCompletionHandler
{
    public function __invoke(ResolutionExecutedMessage $message): void
    {
    }
}
