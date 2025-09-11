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
use App\Dto\Import\CreateImportMappingDto;
use App\Repository\ImportMappingRepository;
use App\State\Import\CreateImportMappingProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImportMappingRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_IMPORT_MAPPING_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_IMPORT_MAPPING_DETAILS')"
        ),
        new Post(
            security: "is_granted('ROLE_IMPORT_MAPPING_CREATE')",
            input: CreateImportMappingDto::class,
            processor: CreateImportMappingProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => ['import_mapping:patch']],
            security: "is_granted('ROLE_IMPORT_MAPPING_UPDATE')"
        ),
    ],
    normalizationContext: ['groups' => ['import_mapping:get']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'name' => 'partial',
        'entityType' => 'exact',
    ]
)]
class ImportMapping
{
    const ID_PREFIX = "IM";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['import_mapping:get', 'import_batch:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['import_mapping:get', 'import_mapping:patch', 'import_batch:get'])]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['import_mapping:get', 'import_mapping:patch', 'import_batch:get'])]
    private ?string $entityType = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['import_mapping:get', 'import_mapping:patch'])]
    private array $mappingConfiguration = [];

    #[ORM\Column]
    #[Groups(['import_mapping:get'])]
    private ?\DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): static
    {
        $this->entityType = $entityType;

        return $this;
    }

    public function getMappingConfiguration(): array
    {
        return $this->mappingConfiguration;
    }

    public function setMappingConfiguration(array $mappingConfiguration): static
    {
        $this->mappingConfiguration = $mappingConfiguration;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}