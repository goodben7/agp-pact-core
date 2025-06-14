<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplaintClassifiedAssignedMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $assignedToUserId,
    )
    {
    }

    public function getComplaintId(): string
    {
        return $this->complaintId;
    }

    public function getAssignedToUserId(): string
    {
        return $this->assignedToUserId;
    }
}
