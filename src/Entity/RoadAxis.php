<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Doctrine\IdGenerator;
use App\Repository\RoadAxisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoadAxisRepository::class)]
#[ApiResource]
class RoadAxis
{
    const ID_PREFIX = "RA";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne]
    private ?Location $startLocation = null;

    #[ORM\ManyToOne]
    private ?Location $endLocation = null;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    private Collection $traversedLocations;

    /**
     * @var Collection<int, SpeciesPrice>
     */
    #[ORM\OneToMany(targetEntity: SpeciesPrice::class, mappedBy: 'roadAxis', orphanRemoval: true)]
    private Collection $speciesPrices;

    public function __construct()
    {
        $this->traversedLocations = new ArrayCollection();
        $this->speciesPrices = new ArrayCollection();
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

    public function getStartLocation(): ?Location
    {
        return $this->startLocation;
    }

    public function setStartLocation(?Location $startLocation): static
    {
        $this->startLocation = $startLocation;

        return $this;
    }

    public function getEndLocation(): ?Location
    {
        return $this->endLocation;
    }

    public function setEndLocation(?Location $endLocation): static
    {
        $this->endLocation = $endLocation;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getTraversedLocations(): Collection
    {
        return $this->traversedLocations;
    }

    public function addTraversedLocation(Location $traversedLocation): static
    {
        if (!$this->traversedLocations->contains($traversedLocation)) {
            $this->traversedLocations->add($traversedLocation);
        }

        return $this;
    }

    public function removeTraversedLocation(Location $traversedLocation): static
    {
        $this->traversedLocations->removeElement($traversedLocation);

        return $this;
    }

    /**
     * @return Collection<int, SpeciesPrice>
     */
    public function getSpeciesPrices(): Collection
    {
        return $this->speciesPrices;
    }

    public function addSpeciesPrice(SpeciesPrice $speciesPrice): static
    {
        if (!$this->speciesPrices->contains($speciesPrice)) {
            $this->speciesPrices->add($speciesPrice);
            $speciesPrice->setRoadAxis($this);
        }

        return $this;
    }

    public function removeSpeciesPrice(SpeciesPrice $speciesPrice): static
    {
        if ($this->speciesPrices->removeElement($speciesPrice)) {
            // set the owning side to null (unless already changed)
            if ($speciesPrice->getRoadAxis() === $this) {
                $speciesPrice->setRoadAxis(null);
            }
        }

        return $this;
    }
}
