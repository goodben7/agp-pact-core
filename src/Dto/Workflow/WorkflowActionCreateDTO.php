<?php

namespace App\Dto\Workflow;

class WorkflowActionCreateDTO
{
    public ?string $name = null;

    public ?string $label = null;

    public ?string $description = null;

    public ?bool $requiresComment = false;

    public ?bool $requiresFile = false;
}
