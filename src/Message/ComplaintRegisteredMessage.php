<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplaintRegisteredMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $complaintEmail,
        public string $complaintPhone,
    )
    {
    }
}
