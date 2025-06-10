<?php

namespace App\Dto\Workflow;


class WorkflowTransitionUpdateDTO
{
    public ?string $fromStepId = null;
    public ?string $toStepId = null;
    public ?string $actionId = null;
    public ?string $roleRequiredId = null;
    public ?string $description = null;
}
