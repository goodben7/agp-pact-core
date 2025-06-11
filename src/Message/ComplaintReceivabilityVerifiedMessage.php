<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplaintReceivabilityVerifiedMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public bool $isReceivable,
        public string $complainantEmail,
        public string $complainantPhone,
    )
    {
    }
}
