<?php

namespace App\Dto\Complaint;

class ComplaintHistoryCreateDTO
{
    public ?string $complaintId = null;
    public ?string $oldWorkflowStepId = null;
    public ?string $newWorkflowStepId = null;
    public ?string $actionId = null;
    public ?string $comments = null;
    public ?string $actorId = null;
}
