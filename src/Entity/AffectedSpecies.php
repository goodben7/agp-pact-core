<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Repository\AffectedSpeciesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffectedSpeciesRepository::class)]
class AffectedSpecies
{
    const ID_PREFIX = "AS";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'affectedSpecies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $speciesType = null;

    #[ORM\Column(nullable: true)]
    private ?float $affectedQuantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $affectedUnit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $assetType = null;

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

    public function getSpeciesType(): ?GeneralParameter
    {
        return $this->speciesType;
    }

    public function setSpeciesType(?GeneralParameter $speciesType): static
    {
        $this->speciesType = $speciesType;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAssetType(): ?GeneralParameter
    {
        return $this->assetType;
    }

    public function setAssetType(?GeneralParameter $assetType): static
    {
        $this->assetType = $assetType;

        return $this;
    }
}
