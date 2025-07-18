<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Workflow\WorkflowActionCreateDTO;
use App\Repository\WorkflowActionRepository;
use App\State\Workflow\WorkflowActionCreateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: WorkflowActionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_WORKFLOW_ACTION_NAME', columns: ['name'])]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['workflow_action:list']],
            security: "is_granted('ROLE_WORKFLOW_ACTION_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_WORKFLOW_ACTION_DETAILS')"
        ),
        new Post(
            security: "is_granted('ROLE_WORKFLOW_ACTION_CREATE')",
            input: WorkflowActionCreateDTO::class,
            processor: WorkflowActionCreateProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_WORKFLOW_ACTION_UPDATE')",
        ),
        new Delete(
            security: "is_granted('ROLE_WORKFLOW_ACTION_DELETE')"
        ),
    ],
    normalizationContext: ['groups' => ['workflow_action:get']]
)]
class WorkflowAction
{
    const ID_PREFIX = "WA";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['workflow_action:get', 'workflow_action:list', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list', 'complaint_history:get', 'complaint_history:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['workflow_action:get', 'workflow_action:list', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list', 'complaint_history:get', 'complaint_history:list'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['workflow_action:get', 'workflow_action:list', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list'])]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['workflow_action:get', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['workflow_action:get', 'workflow_action:list', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list'])]
    private ?bool $requiresComment = null;

    #[ORM\Column]
    #[Groups(['workflow_action:get', 'workflow_action:list', 'workflow_transition:get', 'workflow_transition:list', 'complaint:get', 'complaint:list'])]
    private ?bool $requiresFile = null;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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

    public function isRequiresComment(): ?bool
    {
        return $this->requiresComment;
    }

    public function setRequiresComment(bool $requiresComment): static
    {
        $this->requiresComment = $requiresComment;

        return $this;
    }

    public function isRequiresFile(): ?bool
    {
        return $this->requiresFile;
    }

    public function setRequiresFile(bool $requiresFile): static
    {
        $this->requiresFile = $requiresFile;

        return $this;
    }
}
