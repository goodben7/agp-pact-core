<?php

namespace App\State\Workflow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Workflow\WorkflowTransitionCreateDTO;
use App\Entity\WorkflowTransition;
use App\Manager\WorkflowTransitionManager;

readonly class WorkflowTransitionCreateProcessor implements ProcessorInterface
{
    public function __construct(private WorkflowTransitionManager $manager)
    {
    }

    /** @var WorkflowTransitionCreateDTO $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): WorkflowTransition
    {
        return $this->manager->create($data);
    }
}
