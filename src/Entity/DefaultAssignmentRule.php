<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Doctrine\IdGenerator;
use App\Repository\DefaultAssignmentRuleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DefaultAssignmentRuleRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['default_assignment_rule:list']],
            security: "is_granted('ROLE_DEFAULT_ASSIGNMENT_RULE_LIST')",
            provider: CollectionProvider::class
        ),
        new Get(
            security: "is_granted('ROLE_DEFAULT_ASSIGNMENT_RULE_DETAILS')",
            provider: ItemProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => ['default_assignment_rule:post']],
            security: "is_granted('ROLE_DEFAULT_ASSIGNMENT_RULE_CREATE')",
        ),
        new Patch(
            denormalizationContext: ['groups' => ['default_assignment_rule:patch']],
            security: "is_granted('ROLE_DEFAULT_ASSIGNMENT_RULE_UPDATE')"
        ),
    ],
    normalizationContext: ['groups' => ['default_assignment_rule:get']]
)]
class DefaultAssignmentRule
{
    public const ID_PREFIX = "DA";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'defaultAssignmentRules')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?WorkflowStep $workflowStep = null;

    #[ORM\ManyToOne]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?Location $location = null;

    #[ORM\ManyToOne]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?RoadAxis $roadAxis = null;

    #[ORM\ManyToOne]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?Company $assignedCompany = null;

    #[ORM\ManyToOne]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?Profile $assignedProfile = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?int $priority = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?string $description = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getRoadAxis(): ?RoadAxis
    {
        return $this->roadAxis;
    }

    public function setRoadAxis(?RoadAxis $roadAxis): static
    {
        $this->roadAxis = $roadAxis;

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

    public function getAssignedProfile(): ?Profile
    {
        return $this->assignedProfile;
    }

    public function setAssignedProfile(?Profile $assignedProfile): static
    {
        $this->assignedProfile = $assignedProfile;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): static
    {
        $this->priority = $priority;

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
}
