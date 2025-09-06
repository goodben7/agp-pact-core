<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Doctrine\IdGenerator;
use App\Repository\DefaultAssignmentRuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(nullable: true)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?bool $location = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?bool $roadAxis = null;

    #[ORM\ManyToMany(targetEntity: GeneralParameter::class)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private Collection $assignedProfiles;

    #[ORM\Column(nullable: true)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?int $priority = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['default_assignment_rule:get', 'default_assignment_rule:list', 'default_assignment_rule:post', 'default_assignment_rule:patch'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->assignedProfiles = new ArrayCollection();
    }

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

    public function getLocation(): ?bool
    {
        return $this->location;
    }

    public function setLocation(?bool $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getRoadAxis(): ?bool
    {
        return $this->roadAxis;
    }

    public function setRoadAxis(?bool $roadAxis): static
    {
        $this->roadAxis = $roadAxis;

        return $this;
    }

    /**
     * @return Collection<int, GeneralParameter>
     */
    public function getAssignedProfiles(): Collection
    {
        return $this->assignedProfiles;
    }

    public function addAssignedProfile(GeneralParameter $assignedProfile): static
    {
        if (!$this->assignedProfiles->contains($assignedProfile)) {
            $this->assignedProfiles->add($assignedProfile);
        }

        return $this;
    }

    public function removeAssignedProfile(GeneralParameter $assignedProfile): static
    {
        $this->assignedProfiles->removeElement($assignedProfile);

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
