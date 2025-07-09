<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CompanyRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_COMPANY_LIST")',
            provider: CollectionProvider::class
        ),
        new Get(
            security: 'is_granted("ROLE_COMPANY_DETAILS")',
            provider: ItemProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'company:post',],
            security: 'is_granted("ROLE_COMPANY_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'company:patch',],
            security: 'is_granted("ROLE_COMPANY_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'company:get']
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'name' => 'ipartial',
    'type.id' => 'exact',
    'type.category' => 'exact',
    'type.code' => 'exact',
    'active' => 'exact'
])]
class Company
{
    public const ID_PREFIX = "CN";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['company:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Groups(['company:get', 'company:post', 'company:patch'])]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Groups(['company:get', 'company:post', 'company:patch'])]
    private ?GeneralParameter $type = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email]
    #[Groups(['company:get', 'company:post', 'company:patch'])]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Groups(['company:get', 'company:post', 'company:patch'])]
    private ?string $contactPhone = null;

    #[ORM\Column]
    #[Groups(['company:get', 'company:post', 'company:patch'])]
    private ?bool $active = false;

    /**
     * @var Collection<int, Complaint>
     */
    #[ORM\OneToMany(targetEntity: Complaint::class, mappedBy: 'involvedCompany')]
    private Collection $complaints;

    /**
     * @var Collection<int, Member>
     */
    #[ORM\OneToMany(targetEntity: Member::class, mappedBy: 'company')]
    #[Groups(['company:get'])]
    private Collection $members;

    /**
     * @var Collection<int, RoadAxis>
     */
    #[ORM\ManyToMany(targetEntity: RoadAxis::class)]
    private Collection $roadAxes;

    /**
     * @var Collection<int, ComplaintStepAssignment>
     */
    #[ORM\OneToMany(targetEntity: ComplaintStepAssignment::class, mappedBy: 'assignedCompany', orphanRemoval: true)]
    private Collection $complaintStepAssignments;

    public function __construct()
    {
        $this->complaints = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->roadAxes = new ArrayCollection();
        $this->complaintStepAssignments = new ArrayCollection();
    }

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

    public function getType(): ?GeneralParameter
    {
        return $this->type;
    }

    public function setType(?GeneralParameter $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): static
    {
        $this->contactPhone = $contactPhone;

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

    /**
     * @return Collection<int, Complaint>
     */
    public function getComplaints(): Collection
    {
        return $this->complaints;
    }

    public function addComplaint(Complaint $complaint): static
    {
        if (!$this->complaints->contains($complaint)) {
            $this->complaints->add($complaint);
            $complaint->setInvolvedCompany($this);
        }

        return $this;
    }

    public function removeComplaint(Complaint $complaint): static
    {
        if ($this->complaints->removeElement($complaint)) {
            // set the owning side to null (unless already changed)
            if ($complaint->getInvolvedCompany() === $this) {
                $complaint->setInvolvedCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setCompany($this);
        }

        return $this;
    }

    public function removeMember(Member $member): static
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getCompany() === $this) {
                $member->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RoadAxis>
     */
    public function getRoadAxes(): Collection
    {
        return $this->roadAxes;
    }

    public function addRoadAxe(RoadAxis $roadAxe): static
    {
        if (!$this->roadAxes->contains($roadAxe)) {
            $this->roadAxes->add($roadAxe);
        }

        return $this;
    }

    public function removeRoadAxe(RoadAxis $roadAxe): static
    {
        $this->roadAxes->removeElement($roadAxe);

        return $this;
    }

    /**
     * @return Collection<int, ComplaintStepAssignment>
     */
    public function getComplaintStepAssignments(): Collection
    {
        return $this->complaintStepAssignments;
    }

    public function addComplaintStepAssignment(ComplaintStepAssignment $complaintStepAssignment): static
    {
        if (!$this->complaintStepAssignments->contains($complaintStepAssignment)) {
            $this->complaintStepAssignments->add($complaintStepAssignment);
            $complaintStepAssignment->setAssignedCompany($this);
        }

        return $this;
    }

    public function removeComplaintStepAssignment(ComplaintStepAssignment $complaintStepAssignment): static
    {
        if ($this->complaintStepAssignments->removeElement($complaintStepAssignment)) {
            // set the owning side to null (unless already changed)
            if ($complaintStepAssignment->getAssignedCompany() === $this) {
                $complaintStepAssignment->setAssignedCompany(null);
            }
        }

        return $this;
    }
}
