<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplaintClosedMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $closureReason,
    )
    {
    }
}
