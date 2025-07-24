<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class AssignedMessage implements EventMessageInterface
{
    public function __construct(
        public string  $complaintId,
        public ?string $assignedToCompanyId = null,
    )
    {
    }

    public function getComplaintId(): string
    {
        return $this->complaintId;
    }

    public function getAssignedToCompanyId(): ?string
    {
        return $this->assignedToCompanyId;
    }
}
