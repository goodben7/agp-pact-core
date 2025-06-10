<?php

namespace App\Dto\Workflow;

class WorkflowTransitionCreateDTO
{
    public ?string $fromStepId = null;
    public ?string $toStepId = null;
    public ?string $actionId = null;
    public ?string $roleRequiredId = null;
    public ?string $description = null;
}
