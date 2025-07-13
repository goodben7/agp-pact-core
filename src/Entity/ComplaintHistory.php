<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Doctrine\IdGenerator;
use App\Repository\ComplaintHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ComplaintHistoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['complaint_history:list']],
            security: "is_granted('ROLE_COMPLAINT_HISTORY_LIST')"
        ),
        new Get(
            normalizationContext: ['groups' => ['complaint_history:get']],
            security: "is_granted('ROLE_COMPLAINT_HISTORY_GET')"
        ),
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'complaint.id' => 'exact',
        'oldWorkflowStep.id' => 'exact',
        'newWorkflowStep.id' => 'exact',
        'action.id' => 'exact',
        'actor.id' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'actionDate'
    ]
)]
class ComplaintHistory
{
    const ID_PREFIX = "CH";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?WorkflowStep $oldWorkflowStep = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?WorkflowStep $newWorkflowStep = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?WorkflowAction $action = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?string $comments = null;

    #[ORM\Column]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?\DateTimeImmutable $actionDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint_history:get', 'complaint_history:list'])]
    private ?User $actor = null;

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

    public function getActor(): ?User
    {
        return $this->actor;
    }

    public function setActor(?User $actor): static
    {
        $this->actor = $actor;

        return $this;
    }
}
