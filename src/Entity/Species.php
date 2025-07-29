<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Repository\SpeciesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SpeciesRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['species:list']],
            security: "is_granted('ROLE_SPECIES_LIST')"
        ),
        new Get(
            normalizationContext: ['groups' => ['species:get']],
            security: "is_granted('ROLE_SPECIES_DETAILS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['species:post']],
            security: "is_granted('ROLE_SPECIES_CREATE')",
            processor: PersistProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => ['species:patch']],
            security: "is_granted('ROLE_SPECIES_UPDATE')",
            processor: PersistProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['species:get']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'name' => 'ipartial',
    'category.code' => 'exact',
    'active' => 'exact',
    'deleted' => 'exact',
])]
class Species
{
    public const ID_PREFIX = "ES";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['species:get', 'species:list', 'affected_species:get', 'species_price:get', 'complaint:get', 'complaint:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['species:get', 'species:list', 'species:post', 'species:patch', 'affected_species:get', 'species_price:get', 'complaint:get', 'complaint:list'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['species:get', 'species:post', 'species:patch'])]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['species:get', 'species:list', 'species:post', 'species:patch'])]
    private ?GeneralParameter $category = null;

    #[ORM\Column]
    #[Groups(['species:get', 'species:list', 'species:post', 'species:patch'])]
    private ?bool $active = true;

    #[ORM\Column]
    #[Groups(['species:get'])]
    private ?bool $deleted = false;

    /**
     * @var Collection<int, AffectedSpecies>
     */
    #[ORM\OneToMany(targetEntity: AffectedSpecies::class, mappedBy: 'speciesType')]
    private Collection $affectedInstances;

    /**
     * @var Collection<int, SpeciesPrice>
     */
    #[ORM\OneToMany(targetEntity: SpeciesPrice::class, mappedBy: 'speciesType')]
    private Collection $prices;

    #[ORM\ManyToOne]
    #[Groups(['species:get', 'species:list', 'species:post', 'species:patch'])]
    private ?GeneralParameter $unit = null;

    public function __construct()
    {
        $this->affectedInstances = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->deleted = false;
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

    public function getCategory(): ?GeneralParameter
    {
        return $this->category;
    }

    public function setCategory(?GeneralParameter $category): static
    {
        $this->category = $category;
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

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return Collection<int, AffectedSpecies>
     */
    public function getAffectedInstances(): Collection
    {
        return $this->affectedInstances;
    }

    public function addAffectedInstance(AffectedSpecies $affectedInstance): static
    {
        if (!$this->affectedInstances->contains($affectedInstance)) {
            $this->affectedInstances->add($affectedInstance);
            $affectedInstance->setSpeciesType($this);
        }
        return $this;
    }

    public function removeAffectedInstance(AffectedSpecies $affectedInstance): static
    {
        if ($this->affectedInstances->removeElement($affectedInstance)) {
            // set the owning side to null (unless already changed)
            if ($affectedInstance->getSpeciesType() === $this) {
                $affectedInstance->setSpeciesType(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, SpeciesPrice>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(SpeciesPrice $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setSpeciesType($this);
        }
        return $this;
    }

    public function removePrice(SpeciesPrice $price): static
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getSpeciesType() === $this) {
                $price->setSpeciesType(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
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
}
