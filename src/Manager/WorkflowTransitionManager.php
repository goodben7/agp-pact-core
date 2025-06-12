<?php

namespace App\Manager;


use App\Dto\Workflow\WorkflowTransitionCreateDTO;
use App\Entity\WorkflowTransition;
use Doctrine\ORM\EntityManagerInterface;

readonly class WorkflowTransitionManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(WorkflowTransitionCreateDTO $data): WorkflowTransition
    {
        $transition = (new WorkflowTransition())
            ->setFromStep($data->fromStep)
            ->setToStep($data->toStep)
            ->setAction($data->action)
            ->setRoleRequired($data->roleRequired)
            ->setDescription($data->description);

        $this->em->persist($transition);
        $this->em->flush();

        return $transition;
    }
}
