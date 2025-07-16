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

    public function getComplaintId(): string
    {
        return $this->complaintId;
    }

    public function getClosureReason(): string
    {
        return $this->closureReason;
    }
}
