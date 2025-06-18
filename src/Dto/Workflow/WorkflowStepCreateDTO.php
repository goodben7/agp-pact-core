<?php

namespace App\Dto\Workflow;

class WorkflowStepCreateDTO
{
    public ?string $name = null;

    public ?string $description = null;

    public ?int $position = null;

    public ?bool $isInitial = false;

    public ?bool $isFinal = false;

    public ?bool $active = true;

    public ?int $duration = null;

    public ?int $expectedDuration = null;

    public ?int $emergencyDuration = null;

    public ?string $durationUnitId = null;

    public ?WorkflowStepUIConfigurationCreateDTO $uiConfiguration = null;
}
