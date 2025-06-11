<?php

namespace App\MessageHandler;

use App\Message\ComplaintClassifiedAssignedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateComplaintMeritsHandler
{
    public function __invoke(ComplaintClassifiedAssignedMessage $message): void
    {
    }
}
