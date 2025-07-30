<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Dto\PrejudiceCreateDTO;
use App\Dto\PrejudiceUpdateDTO;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PrejudiceRepository;
use App\State\CreatePrejudiceProcessor;
use App\State\DeletePrejudiceProcessor;
use App\State\UpdatePrejudiceProcessor;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;

#[ORM\Entity(repositoryClass: PrejudiceRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_PREJUDICE_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PREJUDICE_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_PREJUDICE_CREATE")',
            input: PrejudiceCreateDTO::class,
            processor: CreatePrejudiceProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_PREJUDICE_UPDATE")',
            input: PrejudiceUpdateDTO::class,
            processor: UpdatePrejudiceProcessor::class
        ),
        new Delete(
            security: "is_granted('ROLE_PREJUDICE_DELETE')",
            processor: DeletePrejudiceProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'prejudice:get']
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'active' => 'exact',
    'deleted' => 'exact',
])]
class Prejudice
{
    public const ID_PREFIX = "PJ";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['prejudice:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prejudice:get'])]
    private ?string $label = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['prejudice:get'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['prejudice:get'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['prejudice:get'])]
    private ?bool $deleted = false;

    #[ORM\ManyToOne]
    #[Groups(['prejudice:get'])]
    private ?GeneralParameter $assetType = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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
     * Get the value of deleted
     */
    public function isDeleted(): bool|null
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
