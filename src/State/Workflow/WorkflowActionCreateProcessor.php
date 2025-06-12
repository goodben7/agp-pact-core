<?php

namespace App\State\Workflow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Workflow\WorkflowActionCreateDTO;
use App\Entity\WorkflowAction;
use App\Manager\WorkflowActionManager;

readonly class WorkflowActionCreateProcessor implements ProcessorInterface
{
    public function __construct(private WorkflowActionManager $manager)
    {
    }

    /** @var WorkflowActionCreateDTO $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): WorkflowAction
    {
        return $this->manager->create($data);
    }
}
