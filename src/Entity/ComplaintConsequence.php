<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Repository\ComplaintConsequenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplaintConsequenceRepository::class)]
class ComplaintConsequence
{
    const ID_PREFIX = "CC";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'consequences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $consequenceType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $severity = null;

    #[ORM\Column(nullable: true)]
    private ?float $estimatedCost = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $impactDescription = null;

    #[ORM\Column(nullable: true)]
    private ?float $affectedQuantity = null;

    #[ORM\ManyToOne]
    private ?GeneralParameter $affectedUnit = null;

    #[ORM\ManyToOne]
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
