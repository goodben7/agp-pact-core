<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ComplaintStepAssignmentRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ComplaintStepAssignmentRepository::class)]
#[ORM\UniqueConstraint(name: "UNIQ_COMPLAINT_STEP", columns: ["complaint_id", "workflow_step_id"])]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_USER')"),
        new Post(
            denormalizationContext: ['groups' => ['csa:create']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['csa:update']],
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    normalizationContext: ['groups' => ['csa:get']],
)]
class ComplaintStepAssignment
{
    public const ID_PREFIX = "CA";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['csa:get', 'complaint:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'complaintStepAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['csa:get', 'csa:create'])]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['csa:get', 'csa:create', 'complaint:get'])]
    private ?WorkflowStep $workflowStep = null;

    #[ORM\ManyToOne(inversedBy: 'complaintStepAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['csa:get', 'csa:create', 'csa:update', 'complaint:get'])]
    private ?Company $assignedCompany = null;

    #[ORM\Column]
    #[Groups(['csa:get', 'complaint:get'])]
    private ?\DateTimeImmutable $assignedAt = null;

    public function __construct()
    {
        $this->assignedAt = new \DateTimeImmutable();
    }

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

    public function getWorkflowStep(): ?WorkflowStep
    {
        return $this->workflowStep;
    }

    public function setWorkflowStep(?WorkflowStep $workflowStep): static
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    public function getAssignedCompany(): ?Company
    {
        return $this->assignedCompany;
    }

    public function setAssignedCompany(?Company $assignedCompany): static
    {
        $this->assignedCompany = $assignedCompany;

        return $this;
    }

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(\DateTimeImmutable $assignedAt): static
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }
}
