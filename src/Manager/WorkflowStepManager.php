<?php

namespace App\Manager;


use App\Dto\Workflow\WorkflowStepCreateDTO;
use App\Entity\WorkflowStep;
use App\Entity\WorkflowStepUIConfiguration;
use Doctrine\ORM\EntityManagerInterface;

readonly class WorkflowStepManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(WorkflowStepCreateDTO $data): WorkflowStep
    {
        $step = (new WorkflowStep())
            ->setName($data->name)
            ->setDescription($data->description)
            ->setPosition($data->position)
            ->setIsInitial($data->isInitial)
            ->setIsFinal($data->isFinal)
            ->setActive($data->active)
            ->setDuration($data->duration)
            ->setEmergencyDuration($data->emergencyDuration)
            ->setExpectedDuration($data->expectedDuration);

        if ($durationUnit =  $data->durationUnit) {
            $step->setDurationUnit($durationUnit);
        }

        if ($uiConfiguration = $data->uiConfiguration) {
            $uiConfiguration = (new WorkflowStepUIConfiguration())
                ->setMainComponentKey($uiConfiguration->mainComponentKey)
                ->setDescription($uiConfiguration->description)
                ->setTitle($uiConfiguration->title)
                ->setWorkflowStep($step)
                ->setDisplayFields($uiConfiguration->displayFields)
                ->setInputFields($uiConfiguration->inputFields);

            $step->setUiConfiguration($uiConfiguration);
        }

        $this->em->persist($step);
        $this->em->flush();

        return $step;
    }
}
