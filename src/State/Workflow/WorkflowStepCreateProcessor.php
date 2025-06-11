<?php

namespace App\State\Workflow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Workflow\WorkflowStepCreateDTO;
use App\Entity\WorkflowStep;
use App\Manager\WorkflowStepManager;

readonly class WorkflowStepCreateProcessor implements ProcessorInterface
{
    public function __construct(private WorkflowStepManager $manager)
    {
    }

    /** @var WorkflowStepCreateDTO $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): WorkflowStep
    {
        return $this->manager->create($data);
    }
}
