<?php

namespace App\Dto\Workflow;

class WorkflowStepUpdateDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?int $order = null;
    public ?bool $isInitial = null;
    public ?bool $isFinal = null;
    public ?bool $isActive = null;
    public ?int $expectedDuration = null;
    public ?string $durationUnitId = null;
}
