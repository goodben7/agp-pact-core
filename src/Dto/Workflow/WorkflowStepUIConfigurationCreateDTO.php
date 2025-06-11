<?php

namespace App\Dto\Workflow;

use App\Model\Workflow\DisplayFields;
use App\Model\Workflow\InputFields;

class WorkflowStepUIConfigurationCreateDTO
{
    public ?string $mainComponentKey = null;

    public ?string $title = null;

    public ?string $description = null;

    /**
     * @var InputFields[]|null
     */
    public ?array $inputFields = null;

    /**
     * @var DisplayFields[]|null
     */
    public ?array $displayFields = null;
}
