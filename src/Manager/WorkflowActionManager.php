<?php

namespace App\Manager;


use App\Dto\Workflow\WorkflowActionCreateDTO;
use App\Entity\WorkflowAction;
use Doctrine\ORM\EntityManagerInterface;

readonly class WorkflowActionManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(WorkflowActionCreateDTO $data): WorkflowAction
    {
        $action = (new WorkflowAction())
            ->setName($data->name)
            ->setLabel($data->label)
            ->setDescription($data->description)
            ->setRequiresComment($data->requiresComment)
            ->setRequiresFile($data->requiresFile);

        $this->em->persist($action);
        $this->em->flush();

        return $action;
    }
}
