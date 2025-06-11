<?php

namespace App\MessageHandler;

use App\Message\SatisfactionFollowedUpMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AnalyzeSatisfactionFeedbackHandler
{
    public function __invoke(SatisfactionFollowedUpMessage $message): void
    {
    }
}
