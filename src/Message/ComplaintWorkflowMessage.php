<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplaintWorkflowMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $actionName,
        public string $newStepName
    )
    {
    }

    public function getComplaintId(): string
    {
        return $this->complaintId;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function getNewStepName(): string
    {
        return $this->newStepName;
    }
}
