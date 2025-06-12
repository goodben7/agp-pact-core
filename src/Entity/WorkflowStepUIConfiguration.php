<?php

namespace App\Entity;

use App\Repository\WorkflowStepUIConfigurationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowStepUIConfigurationRepository::class)]
class WorkflowStepUIConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'uiConfiguration', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkflowStep $workflowStep = null;

    #[ORM\Column(length: 255)]
    private ?string $mainComponentKey = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?array $displayFields = null;

    #[ORM\Column(nullable: true)]
    private ?array $inputFields = null;

    #[ORM\Column(nullable: true)]
    private ?array $customWidgets = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkflowStep(): ?WorkflowStep
    {
        return $this->workflowStep;
    }

    public function setWorkflowStep(WorkflowStep $workflowStep): static
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    public function getMainComponentKey(): ?string
    {
        return $this->mainComponentKey;
    }

    public function setMainComponentKey(string $mainComponentKey): static
    {
        $this->mainComponentKey = $mainComponentKey;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getDisplayFields(): ?array
    {
        return $this->displayFields;
    }

    public function setDisplayFields(?array $displayFields): static
    {
        $this->displayFields = $displayFields;

        return $this;
    }

    public function getInputFields(): ?array
    {
        return $this->inputFields;
    }

    public function setInputFields(?array $inputFields): static
    {
        $this->inputFields = $inputFields;

        return $this;
    }

    public function getCustomWidgets(): ?array
    {
        return $this->customWidgets;
    }

    public function setCustomWidgets(?array $customWidgets): static
    {
        $this->customWidgets = $customWidgets;

        return $this;
    }
}
