<?php

namespace App\Dto\Workflow;

class WorkflowStepCreateDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?int $order = null;
    public ?bool $isInitial = false;
    public ?bool $isFinal = false;
    public ?bool $isActive = true;
    public ?int $expectedDuration = null;
    public ?string $durationUnitId = null;
}
