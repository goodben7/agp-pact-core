<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplaintEscalatedMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $escalationLevelId,
    )
    {
    }
}
