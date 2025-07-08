<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use App\Provider\Complaint\ComplaintProvider;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ComplaintRepository;
use App\Dto\Complaint\ApplyActionRequest;
use App\Dto\Complaint\ComplaintCreateDTO;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\State\Complaint\CreateComplaintProcessor;
use App\State\Complaint\UpdateComplaintProcessor;
use Symfony\Component\Serializer\Attribute\Groups;
use App\State\Complaint\ComplaintApplyActionProcessor;

#[ORM\Entity(repositoryClass: ComplaintRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['complaint:list']],
            security: "is_granted('ROLE_COMPLAINT_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_COMPLAINT_DETAILS')",
            provider: ComplaintProvider::class
        ),
        new Post(
            //security: "is_granted('ROLE_COMPLAINT_CREATE')",
            input: ComplaintCreateDTO::class,
            processor: CreateComplaintProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_COMPLAINT_UPDATE')",
            processor: UpdateComplaintProcessor::class
        ),
        new Post(
            uriTemplate: '/complaints/{id}/apply-action',
            inputFormats: [
                'json' => ['application/json', 'application/ld+json'],
                'multipart' => ['multipart/form-data']
            ],
            security: "is_granted('ROLE_COMPLAINT_APPLY_ACTION')",
            input: ApplyActionRequest::class,
            processor: ComplaintApplyActionProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['complaint:get']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'complaintType.id' => 'exact',
        'currentWorkflowStep.id' => 'exact',
        'incidentCause.id' => 'exact',
        'roadAxis.id' => 'exact',
        'location.id' => 'exact',
        'complainant.id' => 'exact',
        'currentWorkflowAction.id' => 'exact',
        'isSensitive' => 'exact',
    ]
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'declarationDate',
        'closureDate',
        'incidentDate',
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['declarationDate', 'closureDate', 'incidentDate'])]
class Complaint
{
    public const ID_PREFIX = "CP";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?GeneralParameter $complaintType = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?WorkflowStep $currentWorkflowStep = null;

    #[ORM\Column]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?\DateTimeImmutable $declarationDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?GeneralParameter $incidentCause = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?RoadAxis $roadAxis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $locationDetail = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?Location $location = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?float $longitude = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $receivabilityDecisionJustification = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $meritsAnalysis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $resolutionProposal = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?GeneralParameter $internalResolutionDecision = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $internalDecisionComments = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?GeneralParameter $complainantDecision = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $executionActionsDescription = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $personInChargeOfExecution = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?GeneralParameter $satisfactionFollowUpResult = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $satisfactionFollowUpComments = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?GeneralParameter $escalationLevel = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $escalationComments = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?string $closureReason = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?\DateTimeImmutable $closureDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?Complainant $complainant = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?WorkflowAction $currentWorkflowAction = null;

    /**
     * @var Collection<int, Victim>
     */
    #[ORM\OneToMany(targetEntity: Victim::class, mappedBy: 'complaint', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private Collection $victims;

    /**
     * @var Collection<int, ComplaintConsequence>
     */
    #[ORM\OneToMany(targetEntity: ComplaintConsequence::class, mappedBy: 'complaint', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private Collection $consequences;

    /**
     * @var Collection<int, AttachedFile>
     */
    #[ORM\OneToMany(targetEntity: AttachedFile::class, mappedBy: 'complaint', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private Collection $attachedFiles;

    /**
     * @var Collection<int, AffectedSpecies>
     */
    #[ORM\OneToMany(targetEntity: AffectedSpecies::class, mappedBy: 'complaint', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private Collection $affectedSpecies;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?User $assignedTo = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?User $currentAssignee = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?\DateTimeImmutable $incidentDate = null;

    #[ORM\ManyToOne(inversedBy: 'complaints')]
    private ?Company $involvedCompany = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $proposedResolutionDescription = null;


    #[Groups(['complaint:get'])]
    private Collection $availableActions;

    #[ORM\Column]
    #[Groups(['complaint:get', 'complaint:list'])]
    private ?bool $isSensitive = false;

    #[ORM\Column]
    private ?bool $closed = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $satisfactionComments = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $closureComments = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $executionDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $estimatedCost = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $proposedResolutionEndDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $proposedResolutionStartDate = null;

    public function __construct()
    {
        $this->victims = new ArrayCollection();
        $this->consequences = new ArrayCollection();
        $this->attachedFiles = new ArrayCollection();
        $this->affectedSpecies = new ArrayCollection();
        $this->availableActions = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getComplaintType(): ?GeneralParameter
    {
        return $this->complaintType;
    }

    public function setComplaintType(?GeneralParameter $complaintType): static
    {
        $this->complaintType = $complaintType;

        return $this;
    }

    public function getCurrentWorkflowStep(): ?WorkflowStep
    {
        return $this->currentWorkflowStep;
    }

    public function setCurrentWorkflowStep(?WorkflowStep $currentWorkflowStep): static
    {
        $this->currentWorkflowStep = $currentWorkflowStep;

        return $this;
    }

    public function getDeclarationDate(): ?\DateTimeImmutable
    {
        return $this->declarationDate;
    }

    public function setDeclarationDate(\DateTimeImmutable $declarationDate): static
    {
        $this->declarationDate = $declarationDate;

        return $this;
    }

    public function getIncidentCause(): ?GeneralParameter
    {
        return $this->incidentCause;
    }

    public function setIncidentCause(?GeneralParameter $incidentCause): static
    {
        $this->incidentCause = $incidentCause;

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

    public function getRoadAxis(): ?RoadAxis
    {
        return $this->roadAxis;
    }

    public function setRoadAxis(?RoadAxis $roadAxis): static
    {
        $this->roadAxis = $roadAxis;

        return $this;
    }

    public function getLocationDetail(): ?string
    {
        return $this->locationDetail;
    }

    public function setLocationDetail(?string $locationDetail): static
    {
        $this->locationDetail = $locationDetail;

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

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getReceivabilityDecisionJustification(): ?string
    {
        return $this->receivabilityDecisionJustification;
    }

    public function setReceivabilityDecisionJustification(?string $receivabilityDecisionJustification): static
    {
        $this->receivabilityDecisionJustification = $receivabilityDecisionJustification;

        return $this;
    }

    public function getMeritsAnalysis(): ?string
    {
        return $this->meritsAnalysis;
    }

    public function setMeritsAnalysis(?string $meritsAnalysis): static
    {
        $this->meritsAnalysis = $meritsAnalysis;

        return $this;
    }

    public function getResolutionProposal(): ?string
    {
        return $this->resolutionProposal;
    }

    public function setResolutionProposal(?string $resolutionProposal): static
    {
        $this->resolutionProposal = $resolutionProposal;

        return $this;
    }

    public function getInternalResolutionDecision(): ?GeneralParameter
    {
        return $this->internalResolutionDecision;
    }

    public function setInternalResolutionDecision(?string $internalResolutionDecision): static
    {
        $this->internalResolutionDecision = $internalResolutionDecision;

        return $this;
    }

    public function getInternalDecisionComments(): ?string
    {
        return $this->internalDecisionComments;
    }

    public function setInternalDecisionComments(?string $internalDecisionComments): static
    {
        $this->internalDecisionComments = $internalDecisionComments;

        return $this;
    }

    public function getComplainantDecision(): ?GeneralParameter
    {
        return $this->complainantDecision;
    }

    public function setComplainantDecision(?GeneralParameter $complainantDecision): static
    {
        $this->complainantDecision = $complainantDecision;

        return $this;
    }

    public function getExecutionActionsDescription(): ?string
    {
        return $this->executionActionsDescription;
    }

    public function setExecutionActionsDescription(?string $executionActionsDescription): static
    {
        $this->executionActionsDescription = $executionActionsDescription;

        return $this;
    }

    public function getPersonInChargeOfExecution(): ?string
    {
        return $this->personInChargeOfExecution;
    }

    public function setPersonInChargeOfExecution(?string $personInChargeOfExecution): static
    {
        $this->personInChargeOfExecution = $personInChargeOfExecution;

        return $this;
    }

    public function getSatisfactionFollowUpResult(): ?GeneralParameter
    {
        return $this->satisfactionFollowUpResult;
    }

    public function setSatisfactionFollowUpResult(?GeneralParameter $satisfactionFollowUpResult): static
    {
        $this->satisfactionFollowUpResult = $satisfactionFollowUpResult;

        return $this;
    }

    public function getSatisfactionFollowUpComments(): ?string
    {
        return $this->satisfactionFollowUpComments;
    }

    public function setSatisfactionFollowUpComments(?string $satisfactionFollowUpComments): static
    {
        $this->satisfactionFollowUpComments = $satisfactionFollowUpComments;

        return $this;
    }

    public function getEscalationLevel(): ?GeneralParameter
    {
        return $this->escalationLevel;
    }

    public function setEscalationLevel(?GeneralParameter $escalationLevel): static
    {
        $this->escalationLevel = $escalationLevel;

        return $this;
    }

    public function getEscalationComments(): ?string
    {
        return $this->escalationComments;
    }

    public function setEscalationComments(?string $escalationComments): static
    {
        $this->escalationComments = $escalationComments;

        return $this;
    }

    public function getClosureReason(): ?string
    {
        return $this->closureReason;
    }

    public function setClosureReason(?string $closureReason): static
    {
        $this->closureReason = $closureReason;

        return $this;
    }

    public function getClosureDate(): ?\DateTimeImmutable
    {
        return $this->closureDate;
    }

    public function setClosureDate(?\DateTimeImmutable $closureDate): static
    {
        $this->closureDate = $closureDate;

        return $this;
    }

    public function getComplainant(): ?Complainant
    {
        return $this->complainant;
    }

    public function setComplainant(?Complainant $complainant): static
    {
        $this->complainant = $complainant;

        return $this;
    }

    public function getCurrentWorkflowAction(): ?WorkflowAction
    {
        return $this->currentWorkflowAction;
    }

    public function setCurrentWorkflowAction(?WorkflowAction $currentWorkflowAction): static
    {
        $this->currentWorkflowAction = $currentWorkflowAction;

        return $this;
    }

    /**
     * @return Collection<int, Victim>
     */
    public function getVictims(): Collection
    {
        return $this->victims;
    }

    public function addVictim(Victim $victim): static
    {
        if (!$this->victims->contains($victim)) {
            $this->victims->add($victim);
            $victim->setComplaint($this);
        }

        return $this;
    }

    public function removeVictim(Victim $victim): static
    {
        if ($this->victims->removeElement($victim)) {
            // set the owning side to null (unless already changed)
            if ($victim->getComplaint() === $this) {
                $victim->setComplaint(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ComplaintConsequence>
     */
    public function getConsequences(): Collection
    {
        return $this->consequences;
    }

    public function addConsequence(ComplaintConsequence $consequence): static
    {
        if (!$this->consequences->contains($consequence)) {
            $this->consequences->add($consequence);
            $consequence->setComplaint($this);
        }

        return $this;
    }

    public function removeConsequence(ComplaintConsequence $consequence): static
    {
        if ($this->consequences->removeElement($consequence)) {
            // set the owning side to null (unless already changed)
            if ($consequence->getComplaint() === $this) {
                $consequence->setComplaint(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AttachedFile>
     */
    public function getAttachedFiles(): Collection
    {
        return $this->attachedFiles;
    }

    public function addAttachedFile(AttachedFile $attachedFile): static
    {
        if (!$this->attachedFiles->contains($attachedFile)) {
            $this->attachedFiles->add($attachedFile);
            $attachedFile->setComplaint($this);
        }

        return $this;
    }

    public function removeAttachedFile(AttachedFile $attachedFile): static
    {
        if ($this->attachedFiles->removeElement($attachedFile)) {
            // set the owning side to null (unless already changed)
            if ($attachedFile->getComplaint() === $this) {
                $attachedFile->setComplaint(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AffectedSpecies>
     */
    public function getAffectedSpecies(): Collection
    {
        return $this->affectedSpecies;
    }

    public function addAffectedSpecies(AffectedSpecies $affectedSpecies): static
    {
        if (!$this->affectedSpecies->contains($affectedSpecies)) {
            $this->affectedSpecies->add($affectedSpecies);
            $affectedSpecies->setComplaint($this);
        }

        return $this;
    }

    public function removeAffectedSpecies(AffectedSpecies $affectedSpecies): static
    {
        if ($this->affectedSpecies->removeElement($affectedSpecies)) {
            // set the owning side to null (unless already changed)
            if ($affectedSpecies->getComplaint() === $this) {
                $affectedSpecies->setComplaint(null);
            }
        }

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function getCurrentAssignee(): ?User
    {
        return $this->currentAssignee;
    }

    public function setCurrentAssignee(?User $currentAssignee): static
    {
        $this->currentAssignee = $currentAssignee;

        return $this;
    }

    public function getIncidentDate(): ?\DateTimeImmutable
    {
        return $this->incidentDate;
    }

    public function setIncidentDate(?\DateTimeImmutable $incidentDate): static
    {
        $this->incidentDate = $incidentDate;

        return $this;
    }

    public function getInvolvedCompany(): ?Company
    {
        return $this->involvedCompany;
    }

    public function setInvolvedCompany(?Company $involvedCompany): static
    {
        $this->involvedCompany = $involvedCompany;

        return $this;
    }

    public function getProposedResolutionDescription(): ?string
    {
        return $this->proposedResolutionDescription;
    }

    public function setProposedResolutionDescription(?string $proposedResolutionDescription): static
    {
        $this->proposedResolutionDescription = $proposedResolutionDescription;

        return $this;
    }

    /**
     * @return Collection<int, WorkflowAction>
     */
    public function getAvailableActions(): Collection
    {
        if (!isset($this->availableActions)) {
            $this->availableActions = new ArrayCollection();
        }
        return $this->availableActions;
    }

    public function addAvailableAction(WorkflowAction $workflowAction): static
    {
        if (!isset($this->availableActions)) {
            $this->availableActions = new ArrayCollection();
        }
        if (!$this->availableActions->contains($workflowAction)) {
            $this->availableActions->add($workflowAction);
        }

        return $this;
    }

    public function removeAvailableAction(WorkflowAction $workflowAction): static
    {
        if (!isset($this->availableActions)) {
            $this->availableActions = new ArrayCollection();
        }
        $this->availableActions->removeElement($workflowAction);

        return $this;
    }

    /**
     * Get the value of isSensitive
     */ 
    public function getIsSensitive(): bool|null
    {
        return $this->isSensitive;
    }

    /**
     * Set the value of isSensitive
     *
     * @return  self
     */ 
    public function setIsSensitive(bool $isSensitive): static
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function getSatisfactionComments(): ?string
    {
        return $this->satisfactionComments;
    }

    public function setSatisfactionComments(?string $satisfactionComments): static
    {
        $this->satisfactionComments = $satisfactionComments;

        return $this;
    }

    public function getClosureComments(): ?string
    {
        return $this->closureComments;
    }

    public function setClosureComments(string $closureComments): static
    {
        $this->closureComments = $closureComments;

        return $this;
    }

    public function getExecutionDate(): ?\DateTimeImmutable
    {
        return $this->executionDate;
    }

    public function setExecutionDate(?\DateTimeImmutable $executionDate): static
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    public function getEstimatedCost(): ?string
    {
        return $this->estimatedCost;
    }

    public function setEstimatedCost(?string $estimatedCost): static
    {
        $this->estimatedCost = $estimatedCost;

        return $this;
    }

    public function getProposedResolutionEndDate(): ?\DateTimeImmutable
    {
        return $this->proposedResolutionEndDate;
    }

    public function setProposedResolutionEndDate(?\DateTimeImmutable $proposedResolutionEndDate): static
    {
        $this->proposedResolutionEndDate = $proposedResolutionEndDate;

        return $this;
    }

    public function getProposedResolutionStartDate(): ?\DateTimeImmutable
    {
        return $this->proposedResolutionStartDate;
    }

    public function setProposedResolutionStartDate(?\DateTimeImmutable $proposedResolutionStartDate): static
    {
        $this->proposedResolutionStartDate = $proposedResolutionStartDate;

        return $this;
    }
}
