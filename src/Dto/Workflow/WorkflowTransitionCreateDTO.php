<?php

namespace App\Dto\Workflow;

use App\Entity\WorkflowAction;
use App\Entity\WorkflowStep;

class WorkflowTransitionCreateDTO
{
    public ?WorkflowStep $fromStep = null;

    public ?WorkflowStep $toStep = null;

    public ?WorkflowAction $action = null;

    public ?array $roleRequired = null;

    public ?string $description = null;
}
