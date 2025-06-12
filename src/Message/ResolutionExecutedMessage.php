<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ResolutionExecutedMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
    )
    {
    }
}
