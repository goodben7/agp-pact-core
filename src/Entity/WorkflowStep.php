<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Workflow\WorkflowStepCreateDTO;
use App\Repository\WorkflowStepRepository;
use App\State\Workflow\WorkflowStepCreateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: WorkflowStepRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_WORKFLOW_STEP_NAME', columns: ['name'])]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['workflow_step:list']],
            security: "is_granted('ROLE_WORKFLOW_STEP_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_WORKFLOW_STEP_DETAILS')"
        ),
        new Post(
            security: "is_granted('ROLE_WORKFLOW_STEP_CREATE')",
            input: WorkflowStepCreateDTO::class,
            processor: WorkflowStepCreateProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => ['workflow_step:patch']],
            security: "is_granted('ROLE_WORKFLOW_STEP_UPDATE')"
        )
    ],
    normalizationContext: ['groups' => ['workflow_step:get']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'name' => 'partial',
        'isInitial' => 'exact',
        'isFinal' => 'exact',
        'active' => 'exact'
    ]
)]
class WorkflowStep
{
    const ID_PREFIX = "WS";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['workflow_step:get', 'workflow_step:list', 'workflow_step:patch', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list', 'complaint_history:get', 'complaint_history:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['workflow_step:get', 'workflow_step:list', 'workflow_step:patch', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list', 'complaint_history:get', 'complaint_history:list'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'complaint:get', 'complaint:list'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?int $position = null;

    #[ORM\Column]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?bool $isInitial = null;

    #[ORM\Column]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?bool $isFinal = null;

    #[ORM\Column]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?int $expectedDuration = null;

    #[ORM\ManyToOne]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?GeneralParameter $durationUnit = null;

    #[ORM\OneToOne(mappedBy: 'workflowStep', cascade: ['persist', 'remove'])]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list', 'complaint:get', 'complaint:list'])]
    private ?WorkflowStepUIConfiguration $uiConfiguration = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?int $emergencyDuration = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['workflow_step:get', 'workflow_step:patch', 'workflow_step:list'])]
    private ?int $duration = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function isInitial(): ?bool
    {
        return $this->isInitial;
    }

    public function setIsInitial(bool $isInitial): static
    {
        $this->isInitial = $isInitial;

        return $this;
    }

    public function isFinal(): ?bool
    {
        return $this->isFinal;
    }

    public function setIsFinal(bool $isFinal): static
    {
        $this->isFinal = $isFinal;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getExpectedDuration(): ?int
    {
        return $this->expectedDuration;
    }

    public function setExpectedDuration(?int $expectedDuration): static
    {
        $this->expectedDuration = $expectedDuration;

        return $this;
    }

    public function getDurationUnit(): ?GeneralParameter
    {
        return $this->durationUnit;
    }

    public function setDurationUnit(?GeneralParameter $durationUnit): static
    {
        $this->durationUnit = $durationUnit;

        return $this;
    }

    public function getUIConfiguration(): ?WorkflowStepUIConfiguration
    {
        return $this->uiConfiguration;
    }

    public function setUIConfiguration(WorkflowStepUIConfiguration $uiConfiguration): static
    {
        // set the owning side of the relation if necessary
        if ($uiConfiguration->getWorkflowStep() !== $this) {
            $uiConfiguration->setWorkflowStep($this);
        }

        $this->uiConfiguration = $uiConfiguration;

        return $this;
    }

    public function getEmergencyDuration(): ?int
    {
        return $this->emergencyDuration;
    }

    public function setEmergencyDuration(?int $emergencyDuration): static
    {
        $this->emergencyDuration = $emergencyDuration;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
}
