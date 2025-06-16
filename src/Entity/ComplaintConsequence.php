<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ComplaintConsequenceRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: ComplaintConsequenceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'complaint_consequence:get'],
    operations:[
        new Get(
            security: 'is_granted("ROLE_COMPLAINT_CONSEQUENCE_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_COMPLAINT_CONSEQUENCE_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_COMPLAINT_CONSEQUENCE_CREATE")',
            denormalizationContext: ['groups' => 'complaint_consequence:post',],
            processor: PersistProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_COMPLAINT_CONSEQUENCE_UPDATE")',
            denormalizationContext: ['groups' => 'complaint_consequence:patch',],
            processor: PersistProcessor::class,
        ),
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'consequenceType.code' => 'exact',
        'consequenceType.category' => 'exact',
        'severity.code' => 'exact',
        'severity.category' => 'exact',
        'affectedUnit.code' => 'exact',
        'affectedUnit.category' => 'exact',
        'affectedAssetType.code' => 'exact',
        'affectedAssetType.category' => 'exact',
        'complaint' => 'exact',
    ]
)]
class ComplaintConsequence
{
    public const ID_PREFIX = "CC";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['complaint_consequence:get'] )]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'consequences')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post'] )]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post', 'complaint_consequence:patch'] )]
    private ?GeneralParameter $consequenceType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post', 'complaint_consequence:patch'] )]
    private ?GeneralParameter $severity = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post', 'complaint_consequence:patch'] )]
    private ?float $estimatedCost = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post', 'complaint_consequence:patch'] )]
    private ?string $impactDescription = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post', 'complaint_consequence:patch'] )]
    private ?float $affectedQuantity = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post', 'complaint_consequence:patch'] )]
    private ?GeneralParameter $affectedUnit = null;

    #[ORM\ManyToOne]
    #[Groups(['complaint_consequence:get', 'complaint_consequence:post', 'complaint_consequence:patch'] )]
    private ?GeneralParameter $affectedAssetType = null;

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

    public function getConsequenceType(): ?GeneralParameter
    {
        return $this->consequenceType;
    }

    public function setConsequenceType(?GeneralParameter $consequenceType): static
    {
        $this->consequenceType = $consequenceType;

        return $this;
    }

    public function getSeverity(): ?GeneralParameter
    {
        return $this->severity;
    }

    public function setSeverity(?GeneralParameter $severity): static
    {
        $this->severity = $severity;

        return $this;
    }

    public function getEstimatedCost(): ?float
    {
        return $this->estimatedCost;
    }

    public function setEstimatedCost(?float $estimatedCost): static
    {
        $this->estimatedCost = $estimatedCost;

        return $this;
    }

    public function getImpactDescription(): ?string
    {
        return $this->impactDescription;
    }

    public function setImpactDescription(string $impactDescription): static
    {
        $this->impactDescription = $impactDescription;

        return $this;
    }

    public function getAffectedQuantity(): ?float
    {
        return $this->affectedQuantity;
    }

    public function setAffectedQuantity(?float $affectedQuantity): static
    {
        $this->affectedQuantity = $affectedQuantity;

        return $this;
    }

    public function getAffectedUnit(): ?GeneralParameter
    {
        return $this->affectedUnit;
    }

    public function setAffectedUnit(?GeneralParameter $affectedUnit): static
    {
        $this->affectedUnit = $affectedUnit;

        return $this;
    }

    public function getAffectedAssetType(): ?GeneralParameter
    {
        return $this->affectedAssetType;
    }

    public function setAffectedAssetType(?GeneralParameter $affectedAssetType): static
    {
        $this->affectedAssetType = $affectedAssetType;

        return $this;
    }
}
