<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Repository\ComplaintHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplaintHistoryRepository::class)]
class ComplaintHistory
{
    const ID_PREFIX = "CH";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    private ?WorkflowStep $oldWorkflowStep = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkflowStep $newWorkflowStep = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkflowAction $action = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $actionDate = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getComplaint(): ?Complaint
    {
        return $this->complaint;
    }

    public function setComplaint(?Complaint $complaint): static
    {
        $this->complaint = $complaint;

        return $this;
    }

    public function getOldWorkflowStep(): ?WorkflowStep
    {
        return $this->oldWorkflowStep;
    }

    public function setOldWorkflowStep(?WorkflowStep $oldWorkflowStep): static
    {
        $this->oldWorkflowStep = $oldWorkflowStep;

        return $this;
    }

    public function getNewWorkflowStep(): ?WorkflowStep
    {
        return $this->newWorkflowStep;
    }

    public function setNewWorkflowStep(?WorkflowStep $newWorkflowStep): static
    {
        $this->newWorkflowStep = $newWorkflowStep;

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

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): static
    {
        $this->comments = $comments;

        return $this;
    }

    public function getActionDate(): ?\DateTimeImmutable
    {
        return $this->actionDate;
    }

    public function setActionDate(\DateTimeImmutable $actionDate): static
    {
        $this->actionDate = $actionDate;

        return $this;
    }
}
