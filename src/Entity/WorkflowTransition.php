<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Workflow\WorkflowTransitionCreateDTO;
use App\Repository\WorkflowTransitionRepository;
use App\State\Workflow\WorkflowTransitionCreateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowTransitionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['workflow_transition:list']],
            security: "is_granted('ROLE_WORKFLOW_TRANSITION_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_WORKFLOW_TRANSITION_VIEW')"
        ),
        new Post(
            security: "is_granted('ROLE_WORKFLOW_TRANSITION_CREATE')",
            input: WorkflowTransitionCreateDTO::class,
            processor: WorkflowTransitionCreateProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_WORKFLOW_TRANSITION_UPDATE')",
        ),
    ]
)]
class WorkflowTransition
{
    const ID_PREFIX = "WT";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkflowStep $fromStep = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkflowStep $toStep = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkflowAction $action = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    private ?Profile $roleRequired = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFromStep(): ?WorkflowStep
    {
        return $this->fromStep;
    }

    public function setFromStep(?WorkflowStep $fromStep): static
    {
        $this->fromStep = $fromStep;

        return $this;
    }

    public function getToStep(): ?WorkflowStep
    {
        return $this->toStep;
    }

    public function setToStep(?WorkflowStep $toStep): static
    {
        $this->toStep = $toStep;

        return $this;
    }

    public function getAction(): ?WorkflowAction
    {
        return $this->action;
    }

    public function setAction(?WorkflowAction $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRoleRequired(): ?Profile
    {
        return $this->roleRequired;
    }

    public function setRoleRequired(?Profile $roleRequired): static
    {
        $this->roleRequired = $roleRequired;

        return $this;
    }
}
