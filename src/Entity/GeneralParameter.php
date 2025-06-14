<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\GeneralParameter\GeneralParameterCreateDTO;
use App\Repository\GeneralParameterRepository;
use App\State\GeneralParameter\GeneralParameterCreateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GeneralParameterRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['general_parameter:list']],
            security: "is_granted('ROLE_GENERAL_PARAMETER_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_GENERAL_PARAMETER_DETAILS')"
        ),
        new Post(
            security: "is_granted('ROLE_GENERAL_PARAMETER_CREATE')",
            input: GeneralParameterCreateDTO::class,
            processor: GeneralParameterCreateProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_GENERAL_PARAMETER_UPDATE')",
        ),
        new Delete(
            security: "is_granted('ROLE_GENERAL_PARAMETER_DELETE')",
        ),
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'category' => 'partial',
        'value' => 'partial',
        'code' => 'exact',
        'active' => 'exact',
    ]
)]
class GeneralParameter
{
    const ID_PREFIX = "GP";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['general_parameter:get', 'general_parameter:list', 'road_axis:get', 'road_axis:list', 'location:get', 'location:list', 'complaint:get', 'complaint:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['general_parameter:get', 'general_parameter:list', 'road_axis:get', 'road_axis:list', 'location:get', 'location:list', 'complaint:get', 'complaint:list'])]
    private ?string $category = null;

    #[ORM\Column(length: 255)]
    #[Groups(['general_parameter:get', 'general_parameter:list', 'road_axis:get', 'road_axis:list', 'location:get', 'location:list', 'complaint:get', 'complaint:list'])]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    #[Groups(['general_parameter:get', 'general_parameter:list', 'road_axis:get', 'road_axis:list', 'location:get', 'location:list', 'complaint:get', 'complaint:list'])]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['general_parameter:get', 'general_parameter:list'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['general_parameter:get', 'general_parameter:list', 'road_axis:get', 'road_axis:list'])]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['general_parameter:get', 'general_parameter:list'])]
    private ?int $displayOrder = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(?int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }
}
