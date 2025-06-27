<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Dto\PrejudiceCreateDTO;
use App\Dto\PrejudiceUpdateDTO;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PrejudiceRepository;
use App\State\CreatePrejudiceProcessor;
use App\State\DeletePrejudiceProcessor;
use App\State\UpdatePrejudiceProcessor;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;

#[ORM\Entity(repositoryClass: PrejudiceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'prejudice:get'],
    operations:[
        new Get(
            security: 'is_granted("ROLE_PREJUDICE_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PREJUDICE_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_PREJUDICE_CREATE")',
            input: PrejudiceCreateDTO::class,
            processor: CreatePrejudiceProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_PREJUDICE_UPDATE")',
            input: PrejudiceUpdateDTO::class,
            processor: UpdatePrejudiceProcessor::class,
        ),
        new Delete(
            security: "is_granted('ROLE_PREJUDICE_DELETE')",
            processor: DeletePrejudiceProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'category.code' => 'exact',
    'category.category' => 'exact',
    'complaintType.code' => 'exact',
    'complaintType.category' => 'exact',
    'active' => 'exact',
    'deleted' => 'exact',
])]
class Prejudice
{
    public const ID_PREFIX = "PJ";

    #[ORM\Id]
    #[ORM\GeneratedValue( strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)] 
    #[Groups(['prejudice:get'])]  
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prejudice:get'])]  
    private ?string $label = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['prejudice:get'])]  
    private ?GeneralParameter $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['prejudice:get'])]  
    private ?GeneralParameter $complaintType = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['prejudice:get'])]  
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['prejudice:get'])]  
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['prejudice:get'])]
    private ?bool $deleted = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['prejudice:get'])]  
    private ?GeneralParameter $incidentCause = null;

    /**
     * @var Collection<int, PrejudiceConsequence>
     */
    #[ORM\OneToMany(targetEntity: PrejudiceConsequence::class, mappedBy: 'prejudice', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['prejudice:get'])]
    private Collection $consequences;

    public function __construct()
    {
        $this->consequences = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getCategory(): ?GeneralParameter
    {
        return $this->category;
    }

    public function setCategory(?GeneralParameter $category): static
    {
        $this->category = $category;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
     * Get the value of deleted
     */ 
    public function isDeleted(): bool|null
    {
        return $this->deleted;
    }

    /**
     * Set the value of deleted
     *
     * @return  self
     */ 
    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the value of incidentCause
     */ 
    public function getIncidentCause(): GeneralParameter|null
    {
        return $this->incidentCause;
    }

    /**
     * Set the value of incidentCause
     *
     * @return  self
     */ 
    public function setIncidentCause(?GeneralParameter $incidentCause): static
    {
        $this->incidentCause = $incidentCause;

        return $this;
    }

    /**
     * @return Collection<int, PrejudiceConsequence>
     */
    public function getConsequences(): Collection
    {
        return $this->consequences;
    }

    public function addConsequence(PrejudiceConsequence $consequence): static
    {
        if (!$this->consequences->contains($consequence)) {
            $this->consequences->add($consequence);
            $consequence->setPrejudice($this);
        }

        return $this;
    }

    public function removeConsequence(PrejudiceConsequence $consequence): static
    {
        if ($this->consequences->removeElement($consequence)) {
            // set the owning side to null (unless already changed)
            if ($consequence->getPrejudice() === $this) {
                $consequence->setPrejudice(null);
            }
        }

        return $this;
    }
}
