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
use App\Repository\AffectedSpeciesRepository;
use App\Dto\Complaint\AffectedSpeciesCreateDTO;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\State\AffectedSpecies\CreateAffectedSpeciesProcessor;

#[ORM\Entity(repositoryClass: AffectedSpeciesRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['affected_species:list']],
            security: "is_granted('ROLE_AFFECTED_SPECIES_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_AFFECTED_SPECIES_DETAILS')"
        ),
        new Post(
            security: "is_granted('ROLE_AFFECTED_SPECIES_CREATE')",
            input: AffectedSpeciesCreateDTO::class,
            processor: CreateAffectedSpeciesProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_AFFECTED_SPECIES_UPDATE')"
        ),
    ],
    normalizationContext: ['groups' => ['affected_species:get']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'complaint.id' => 'exact',
        'speciesType.code' => 'exact',
        'affectedUnit.code' => 'exact',
        'assetType.code' => 'exact'
    ]
)]
class AffectedSpecies
{
    public const  ID_PREFIX = "AS";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['affected_species:list', 'affected_species:get', 'complaint:get', 'complaint:list'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'affectedSpecies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['affected_species:list', 'affected_species:get', 'complaint:get', 'complaint:list'])]
    private ?GeneralParameter $speciesType = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['affected_species:list', 'affected_species:get', 'complaint:get', 'complaint:list'])]
    private ?float $affectedQuantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['affected_species:list', 'affected_species:get', 'complaint:get', 'complaint:list'])]
    private ?GeneralParameter $affectedUnit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['affected_species:list', 'affected_species:get', 'complaint:get', 'complaint:list'])]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['affected_species:list', 'affected_species:get', 'complaint:get', 'complaint:list'])]
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

    public function __toString(): string
    {
        return $this->getSpeciesType()->getValue();
    }
}
