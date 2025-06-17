<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\SpeciesPriceRepository;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: SpeciesPriceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['species_price:list']],
            security: "is_granted('ROLE_SPECIES_PRICE_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_SPECIES_PRICE_DETAILS')"
        ),
        new Post(
            security: "is_granted('ROLE_SPECIES_PRICE_CREATE')",
            denormalizationContext: ['groups' => 'species_price:post',],
            processor: PersistProcessor::class,
        ),
        new Patch(
            security: "is_granted('ROLE_SPECIES_PRICE_UPDATE')",
            denormalizationContext: ['groups' =>'species_price:patch',],
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => ['species_price:get']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'speciesType.code' => 'exact',
        'roadAxis.code' => 'exact',
        'unit.code' => 'exact',
        'currency.code' => 'exact',
        'effectiveDate' => 'exact',
        'expirationDate' => 'exact'
    ]
)]
class SpeciesPrice
{
    public const ID_PREFIX = "SP";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['species_price:list', 'species_price:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['species_price:list', 'species_price:get', 'species_price:post','species_price:patch'])]
    private ?GeneralParameter $speciesType = null;

    #[ORM\ManyToOne(inversedBy: 'speciesPrices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['species_price:list', 'species_price:get', 'species_price:post','species_price:patch'])]
    private ?RoadAxis $roadAxis = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['species_price:list', 'species_price:get', 'species_price:post','species_price:patch'])]
    private ?float $pricePerUnit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['species_price:list', 'species_price:get', 'species_price:post','species_price:patch'])]
    private ?GeneralParameter $unit = null;

    #[ORM\ManyToOne]
    #[Groups(['species_price:list', 'species_price:get', 'species_price:post','species_price:patch'])]
    private ?GeneralParameter $currency = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['species_price:list', 'species_price:get', 'species_price:post','species_price:patch'])]
    private ?\DateTimeImmutable $effectiveDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['species_price:list', 'species_price:get', 'species_price:post','species_price:patch'])]
    private ?\DateTimeImmutable $expirationDate = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getRoadAxis(): ?RoadAxis
    {
        return $this->roadAxis;
    }

    public function setRoadAxis(?RoadAxis $roadAxis): static
    {
        $this->roadAxis = $roadAxis;

        return $this;
    }

    public function getPricePerUnit(): ?float
    {
        return $this->pricePerUnit;
    }

    public function setPricePerUnit(?float $pricePerUnit): static
    {
        $this->pricePerUnit = $pricePerUnit;

        return $this;
    }

    public function getUnit(): ?GeneralParameter
    {
        return $this->unit;
    }

    public function setUnit(?GeneralParameter $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getCurrency(): ?GeneralParameter
    {
        return $this->currency;
    }

    public function setCurrency(?GeneralParameter $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getEffectiveDate(): ?\DateTimeImmutable
    {
        return $this->effectiveDate;
    }

    public function setEffectiveDate(?\DateTimeImmutable $effectiveDate): static
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeImmutable $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
}
