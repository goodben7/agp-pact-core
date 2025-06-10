<?php

namespace App\Dto\Workflow;

class WorkflowActionUpdateDTO
{
    public ?string $name = null;
    public ?string $label = null;
    public ?string $description = null;
    public ?bool $requiresComment = null;
    public ?bool $requiresFile = null;
}
