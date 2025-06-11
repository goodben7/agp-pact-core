<?php

namespace App\Manager;


use App\Constant\GeneralParameterCategory;
use App\Dto\Workflow\WorkflowStepCreateDTO;
use App\Entity\GeneralParameter;
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
            ->setExpectedDuration($data->expectedDuration);

        if ($data->durationUnitId) {
            $durationUnit = $this->em->getRepository(GeneralParameter::class)->findOneBy(['id' => $data->durationUnitId, 'category' => GeneralParameterCategory::DURATION_UNIT]);
            if (!$durationUnit)
                throw new \InvalidArgumentException('Duration unit not found');
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
