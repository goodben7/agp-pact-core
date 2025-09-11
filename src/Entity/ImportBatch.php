<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Import\CreateImportBatchDto;
use App\Repository\ImportBatchRepository;
use App\State\Import\CreateImportBatchProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ImportBatchRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_IMPORT_BATCH_LIST')",
            provider: CollectionProvider::class
        ),
        new Get(
            security: "is_granted('ROLE_IMPORT_BATCH_DETAILS')",
            provider: ItemProvider::class
        ),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            security: "is_granted('ROLE_IMPORT_CREATE')",
            input: CreateImportBatchDto::class,
            processor: CreateImportBatchProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['import_batch:get']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'entityType' => 'exact',
        'status' => 'exact',
        'uploadedBy.id' => 'exact',
        'mapping.id' => 'exact',
    ]
)]
class ImportBatch
{
    const ID_PREFIX = "IB";

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_PARTIAL_SUCCESS = 'partial_success';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['import_batch:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[Vich\UploadableField(mapping: 'import_batches', fileNameProperty: 'filePath', originalName: 'originalFilename')]
    private ?File $file = null;

    #[ORM\Column(length: 255)]
    #[Groups(['import_batch:get'])]
    private ?string $originalFilename = null;

    #[ORM\Column(length: 100)]
    #[Groups(['import_batch:get'])]
    private ?string $entityType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['import_batch:get'])]
    private ?ImportMapping $mapping = null;

    #[ORM\Column(length: 20)]
    #[Groups(['import_batch:get'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column]
    #[Groups(['import_batch:get'])]
    private int $totalItems = 0;

    #[ORM\Column]
    #[Groups(['import_batch:get'])]
    private int $processedItems = 0;

    #[ORM\Column]
    #[Groups(['import_batch:get'])]
    private int $successfulItems = 0;

    #[ORM\Column]
    #[Groups(['import_batch:get'])]
    private int $failedItems = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['import_batch:get'])]
    private ?User $uploadedBy = null;

    #[ORM\Column]
    #[Groups(['import_batch:get'])]
    private ?\DateTimeImmutable $uploadedAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['import_batch:get'])]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\OneToMany(targetEntity: ImportItem::class, mappedBy: 'batch', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->uploadedAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file)
            $this->uploadedAt = new \DateTimeImmutable();
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): static
    {
        $this->originalFilename = $originalFilename;

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

    public function getMapping(): ?ImportMapping
    {
        return $this->mapping;
    }

    public function setMapping(?ImportMapping $mapping): static
    {
        $this->mapping = $mapping;

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

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $totalItems): static
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    public function getProcessedItems(): int
    {
        return $this->processedItems;
    }

    public function setProcessedItems(int $processedItems): static
    {
        $this->processedItems = $processedItems;

        return $this;
    }

    public function getSuccessfulItems(): int
    {
        return $this->successfulItems;
    }

    public function setSuccessfulItems(int $successfulItems): static
    {
        $this->successfulItems = $successfulItems;

        return $this;
    }

    public function getFailedItems(): int
    {
        return $this->failedItems;
    }

    public function setFailedItems(int $failedItems): static
    {
        $this->failedItems = $failedItems;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): static
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * @return Collection<int, ImportItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ImportItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setBatch($this);
        }
        return $this;
    }

    public function removeItem(ImportItem $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getBatch() === $this) {
                $item->setBatch(null);
            }
        }
        return $this;
    }

    public function incrementProcessedItems(): void
    {
        $this->processedItems++;
    }

    public function incrementSuccessfulItems(): void
    {
        $this->successfulItems++;
    }

    public function incrementFailedItems(): void
    {
        $this->failedItems++;
    }

    public function isCompleted(): bool
    {
        return $this->processedItems === $this->totalItems;
    }
}