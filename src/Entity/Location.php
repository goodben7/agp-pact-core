<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Location\LocationCreateDTO;
use App\Repository\LocationRepository;
use App\State\Location\LocationCreateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['location:list']],
            security: "is_granted('ROLE_LOCATION_LIST')",
        ),
        new Get(
            security: "is_granted('ROLE_LOCATION_VIEW')",
        ),
        new Post(
            security: "is_granted('ROLE_LOCATION_CREATE')",
            input: LocationCreateDTO::class,
            processor: LocationCreateProcessor::class,
        ),
        new Patch(
            security: "is_granted('ROLE_LOCATION_UPDATE')",
        ),
    ],
    normalizationContext: ['groups' => ['location:get']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'name' => 'partial',
        'level.id' => 'exact',
        'level.category' => 'exact',
        'parent.id' => 'exact',
        'code' => 'exact',
        'active' => 'exact'
    ]
)]
class Location
{
    const ID_PREFIX = "LC";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['location:get', 'location:list', 'road_axis:get', 'road_axis:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['location:get', 'location:list', 'road_axis:get', 'road_axis:list'])]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['location:get', 'location:list', 'road_axis:get', 'road_axis:list'])]
    private ?GeneralParameter $level = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[Groups(['location:get', 'location:list', 'road_axis:get', 'road_axis:list'])]
    private ?self $parent = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['location:get', 'location:list', 'road_axis:get', 'road_axis:list'])]
    private ?string $code = null;

    #[ORM\Column]
    #[Groups(['location:get', 'location:list', 'road_axis:get', 'road_axis:list'])]
    private ?bool $active = null;

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

    public function getLevel(): ?GeneralParameter
    {
        return $this->level;
    }

    public function setLevel(?GeneralParameter $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

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
}
