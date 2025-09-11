<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Doctrine\IdGenerator;
use App\Repository\ImportItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImportItemRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_IMPORT_ITEM_LIST')"
        ),
        new Get(
            security: "is_granted('ROLE_IMPORT_ITEM_DETAILS')"
        ),
    ],
    normalizationContext: ['groups' => ['import_item:get']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'status' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['processedAt'])]
class ImportItem
{
    const ID_PREFIX = "II";

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['import_item:get', 'import_batch:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ImportBatch $batch = null;

    #[ORM\Column]
    #[Groups(['import_item:get'])]
    private array $rowData = [];

    #[ORM\Column(length: 20)]
    #[Groups(['import_item:get', 'import_batch:get'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['import_item:get', 'import_batch:get'])]
    private ?string $errorMessage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['import_item:get'])]
    private ?\DateTimeImmutable $processedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getBatch(): ?ImportBatch
    {
        return $this->batch;
    }

    public function setBatch(?ImportBatch $batch): static
    {
        $this->batch = $batch;

        return $this;
    }

    public function getRowData(): array
    {
        return $this->rowData;
    }

    public function setRowData(array $rowData): static
    {
        $this->rowData = $rowData;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;

        return $this;
    }
}