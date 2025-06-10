<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Repository\SpeciesPriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpeciesPriceRepository::class)]
class SpeciesPrice
{
    const ID_PREFIX = "SP";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $speciesType = null;

    #[ORM\ManyToOne(inversedBy: 'speciesPrices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RoadAxis $roadAxis = null;

    #[ORM\Column(nullable: true)]
    private ?float $pricePerUnit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $unit = null;

    #[ORM\ManyToOne]
    private ?GeneralParameter $currency = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $effectiveDate = null;

    #[ORM\Column(nullable: true)]
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
