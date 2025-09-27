<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\RoadAxisRepository;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\Location\RoadAxisCreateDTO;
use App\Dto\Location\RoadAxisUpdateDTO;
use Doctrine\Common\Collections\Collection;
use App\State\Location\CreateRoadAxisProcessor;
use App\State\Location\UpdateRoadAxisProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RoadAxisRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['road_axis:list']],
        ),
        new Get(),
        new Post(
            security: "is_granted('ROLE_ROAD_AXIS_CREATE')",
            input: RoadAxisCreateDTO::class,
            processor: CreateRoadAxisProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_ROAD_AXIS_UPDATE')",
            input: RoadAxisUpdateDTO::class,
            processor: UpdateRoadAxisProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['road_axis:get']],
)]
class RoadAxis
{
    public const ID_PREFIX = "RA";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['road_axis:get', 'road_axis:list', 'complaint:get', 'complaint:list', 'default_assignment_rule:get', 'default_assignment_rule:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['road_axis:get', 'road_axis:list', 'complaint:get', 'complaint:list', 'default_assignment_rule:get', 'default_assignment_rule:list'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['road_axis:get', 'road_axis:list', 'complaint:get', 'complaint:list'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['road_axis:get', 'road_axis:list', 'complaint:get', 'complaint:list'])]
    private ?bool $active = null;
    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[Groups(['road_axis:get', 'road_axis:list'])]
    private Collection $traversedLocations;

    /**
     * @var Collection<int, SpeciesPrice>
     */
    #[ORM\OneToMany(targetEntity: SpeciesPrice::class, mappedBy: 'roadAxis', orphanRemoval: true)]
    private Collection $speciesPrices;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[ORM\JoinTable(name: 'road_axis_province')]
    #[Groups(['road_axis:get', 'road_axis:list'])]
    private Collection $province;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[ORM\JoinTable(name: 'road_axis_territory')]
    #[Groups(['road_axis:get', 'road_axis:list'])]
    private Collection $territory;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[ORM\JoinTable(name: 'road_axis_commune')]
    #[Groups(['road_axis:get', 'road_axis:list'])]
    private Collection $commune;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[ORM\JoinTable(name: 'road_axis_quartier')]
    #[Groups(['road_axis:get', 'road_axis:list'])]
    private Collection $quartier;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[ORM\JoinTable(name: 'road_axis_city')]
    #[Groups(['road_axis:get', 'road_axis:list'])]
    private Collection $city;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[ORM\JoinTable(name: 'road_axis_secteur')]
    #[Groups(['road_axis:get', 'road_axis:list'])]
    private Collection $secteur;

    public function __construct()
    {
        $this->traversedLocations = new ArrayCollection();
        $this->speciesPrices = new ArrayCollection();
        $this->province = new ArrayCollection();
        $this->territory = new ArrayCollection();
        $this->commune = new ArrayCollection();
        $this->quartier = new ArrayCollection();
        $this->city = new ArrayCollection();
        $this->secteur = new ArrayCollection();
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

    /**
     * @return Collection<int, Location>
     */
    public function getProvince(): Collection
    {
        return $this->province;
    }

    public function addProvince(Location $province): static
    {
        if (!$this->province->contains($province)) {
            $this->province->add($province);
        }

        return $this;
    }

    public function removeProvince(Location $province): static
    {
        $this->province->removeElement($province);

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getTerritory(): Collection
    {
        return $this->territory;
    }

    public function addTerritory(Location $territory): static
    {
        if (!$this->territory->contains($territory)) {
            $this->territory->add($territory);
        }

        return $this;
    }

    public function removeTerritory(Location $territory): static
    {
        $this->territory->removeElement($territory);

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getCommune(): Collection
    {
        return $this->commune;
    }

    public function addCommune(Location $commune): static
    {
        if (!$this->commune->contains($commune)) {
            $this->commune->add($commune);
        }

        return $this;
    }

    public function removeCommune(Location $commune): static
    {
        $this->commune->removeElement($commune);

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getQuartier(): Collection
    {
        return $this->quartier;
    }

    public function addQuartier(Location $quartier): static
    {
        if (!$this->quartier->contains($quartier)) {
            $this->quartier->add($quartier);
        }

        return $this;
    }

    public function removeQuartier(Location $quartier): static
    {
        $this->quartier->removeElement($quartier);

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getCity(): Collection
    {
        return $this->city;
    }

    public function addCity(Location $city): static
    {
        if (!$this->city->contains($city)) {
            $this->city->add($city);
        }

        return $this;
    }

    public function removeCity(Location $city): static
    {
        $this->city->removeElement($city);

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getSecteur(): Collection
    {
        return $this->secteur;
    }

    public function addSecteur(Location $secteur): static
    {
        if (!$this->secteur->contains($secteur)) {
            $this->secteur->add($secteur);
        }

        return $this;
    }

    public function removeSecteur(Location $secteur): static
    {
        $this->secteur->removeElement($secteur);

        return $this;
    }
}
