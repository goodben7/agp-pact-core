<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CauseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CauseRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['cause:list']],
            security: 'is_granted("ROLE_CAUSE_LIST")',
            provider: CollectionProvider::class
        ),
        new Get(
            normalizationContext: ['groups' => ['cause:get']],
            security: 'is_granted("ROLE_CAUSE_DETAILS")',
            provider: ItemProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => ['cause:post']],
            security: 'is_granted("ROLE_CAUSE_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['cause:patch']],
            security: 'is_granted("ROLE_CAUSE_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => ['cause:get']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'value' => 'partial',
        'code' => 'exact',
        'assetType.code' => 'exact',
        'assetType.category' => 'exact',
    ]
)]
#[ApiFilter(
    BooleanFilter::class,
    properties: [
        'active',
        'deleted',
    ])]
class Cause
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['cause:get', 'cause:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['cause:get', 'cause:list', 'cause:post', 'cause:patch'])]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    #[Groups(['cause:get', 'cause:list', 'cause:post', 'cause:patch'])]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['cause:get', 'cause:list', 'cause:post', 'cause:patch'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['cause:get', 'cause:list', 'cause:post', 'cause:patch'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['cause:get', 'cause:list'])]
    private ?bool $deleted = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cause:get', 'cause:list', 'cause:post', 'cause:patch'])]
    private ?GeneralParameter $assetType = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

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
